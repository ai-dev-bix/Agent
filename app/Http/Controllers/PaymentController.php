<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['stripeWebhook', 'paypalWebhook']);
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Create Stripe payment intent
     */
    public function createStripePayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:credit_packages,id',
        ]);

        $package = CreditPackage::findOrFail($request->package_id);
        $user = auth()->user();

        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'credit_package_id' => $package->id,
                'payment_method' => 'stripe',
                'amount' => $package->price,
                'currency' => $package->currency,
                'credits_purchased' => $package->credits,
                'bonus_credits' => $package->bonus_credits,
                'status' => 'pending',
            ]);

            // Create Stripe PaymentIntent
            $paymentIntent = PaymentIntent::create([
                'amount' => $package->price * 100, // Convert to cents
                'currency' => strtolower($package->currency),
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $user->id,
                    'package_id' => $package->id,
                ],
                'description' => "Credits purchase: {$package->name}",
            ]);

            $payment->update([
                'gateway_transaction_id' => $paymentIntent->id,
            ]);

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create PayPal payment
     */
    public function createPayPalPayment(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:credit_packages,id',
        ]);

        $package = CreditPackage::findOrFail($request->package_id);
        $user = auth()->user();

        try {
            // Create payment record
            $payment = Payment::create([
                'user_id' => $user->id,
                'credit_package_id' => $package->id,
                'payment_method' => 'paypal',
                'amount' => $package->price,
                'currency' => $package->currency,
                'credits_purchased' => $package->credits,
                'bonus_credits' => $package->bonus_credits,
                'status' => 'pending',
            ]);

            // PayPal configuration
            $paypalConfig = config('services.paypal');
            $mode = $paypalConfig['mode'];
            $clientId = $paypalConfig[$mode]['client_id'];

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'paypal_client_id' => $clientId,
                'amount' => $package->price,
                'currency' => $package->currency,
                'description' => "Credits purchase: {$package->name}",
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle successful PayPal payment
     */
    public function paypalSuccess(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'paypal_order_id' => 'required|string',
        ]);

        $payment = Payment::findOrFail($request->payment_id);

        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            // Update payment with PayPal order ID
            $payment->update([
                'gateway_transaction_id' => $request->paypal_order_id,
                'gateway_response' => $request->all(),
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            // Add credits to user
            $payment->markAsCompleted();

            return redirect()->route('credits.index')
                ->with('success', 'Payment successful! Credits have been added to your account.');

        } catch (\Exception $e) {
            return redirect()->route('credits.purchase')
                ->with('error', 'Payment processing failed. Please contact support.');
        }
    }

    /**
     * Create bank transfer payment request
     */
    public function createBankTransfer(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:credit_packages,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $package = CreditPackage::findOrFail($request->package_id);
        $user = auth()->user();

        $payment = Payment::create([
            'user_id' => $user->id,
            'credit_package_id' => $package->id,
            'payment_method' => 'bank_transfer',
            'amount' => $package->price,
            'currency' => $package->currency,
            'credits_purchased' => $package->credits,
            'bonus_credits' => $package->bonus_credits,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        return redirect()->route('payments.bank-transfer-instructions', $payment)
            ->with('success', 'Bank transfer request created. Please follow the instructions to complete payment.');
    }

    /**
     * Show bank transfer instructions
     */
    public function bankTransferInstructions(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($payment->payment_method !== 'bank_transfer') {
            abort(404);
        }

        return view('payments.bank-transfer', compact('payment'));
    }

    /**
     * Handle Stripe webhooks
     */
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            return response('Webhook signature verification failed.', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event['data']['object'];
                $this->handleStripePaymentSuccess($paymentIntent);
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event['data']['object'];
                $this->handleStripePaymentFailed($paymentIntent);
                break;
            default:
                // Unhandled event type
        }

        return response('Success', 200);
    }

    /**
     * Display payment history
     */
    public function history()
    {
        $payments = auth()->user()->payments()
            ->with('creditPackage')
            ->latest()
            ->paginate(20);

        return view('payments.history', compact('payments'));
    }

    /**
     * Show payment details
     */
    public function show(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Cancel pending payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Cannot cancel this payment.');
        }

        $payment->update(['status' => 'cancelled']);

        return redirect()->route('payments.history')
            ->with('success', 'Payment cancelled successfully.');
    }

    private function handleStripePaymentSuccess($paymentIntent)
    {
        $paymentId = $paymentIntent['metadata']['payment_id'] ?? null;
        
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                    'gateway_response' => $paymentIntent,
                ]);

                // Add credits to user
                $payment->markAsCompleted();
            }
        }
    }

    private function handleStripePaymentFailed($paymentIntent)
    {
        $paymentId = $paymentIntent['metadata']['payment_id'] ?? null;
        
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'failed',
                    'gateway_response' => $paymentIntent,
                ]);
            }
        }
    }
}
