<?php

namespace App\Http\Controllers;

use App\Models\CreditPackage;
use App\Models\UserCredit;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display credit packages for purchase
     */
    public function index()
    {
        $packages = CreditPackage::active()
            ->ordered()
            ->get();
        
        $user = auth()->user();
        $creditHistory = $user->creditHistory()
            ->latest()
            ->limit(10)
            ->get();

        return view('credits.index', compact('packages', 'user', 'creditHistory'));
    }

    /**
     * Display credit purchase page
     */
    public function purchase()
    {
        $packages = CreditPackage::active()
            ->ordered()
            ->get();

        return view('credits.purchase', compact('packages'));
    }

    /**
     * Display credit history
     */
    public function history()
    {
        $creditHistory = auth()->user()->creditHistory()
            ->latest()
            ->paginate(20);

        return view('credits.history', compact('creditHistory'));
    }

    /**
     * Display credit usage statistics
     */
    public function usage()
    {
        $user = auth()->user();
        
        // Get usage statistics
        $totalUsed = $user->creditHistory()
            ->where('type', 'usage')
            ->sum('credits') * -1; // Convert negative to positive

        $totalPurchased = $user->creditHistory()
            ->where('type', 'purchase')
            ->sum('credits');

        $thisMonthUsage = $user->creditHistory()
            ->where('type', 'usage')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('credits') * -1;

        $dailyUsage = $user->creditHistory()
            ->where('type', 'usage')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(ABS(credits)) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlyUsage = $user->creditHistory()
            ->where('type', 'usage')
            ->whereDate('created_at', '>=', now()->subMonths(12))
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(ABS(credits)) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('credits.usage', compact(
            'user',
            'totalUsed',
            'totalPurchased',
            'thisMonthUsage',
            'dailyUsage',
            'monthlyUsage'
        ));
    }

    /**
     * API endpoint to get current credit balance
     */
    public function balance()
    {
        $user = auth()->user();
        
        return response()->json([
            'credits' => $user->credits,
            'free_messages_used' => $user->free_messages_used,
            'free_messages_limit' => $user->free_messages_limit,
            'can_send_free_message' => $user->canSendFreeMessage(),
            'has_credits' => $user->hasCredits(),
        ]);
    }

    /**
     * Gift credits to a user (admin only)
     */
    public function gift(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'credits' => 'required|integer|min:1|max:10000',
            'reason' => 'required|string|max:255',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        
        $user->addCredits(
            $request->credits,
            $request->reason,
            'bonus',
            'admin_gift',
            auth()->id()
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully gifted {$request->credits} credits to {$user->name}",
            'user_credits' => $user->fresh()->credits,
        ]);
    }

    /**
     * Transfer credits between users
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
            'credits' => 'required|integer|min:1|max:1000',
            'message' => 'nullable|string|max:255',
        ]);

        $sender = auth()->user();
        $recipient = \App\Models\User::where('email', $request->recipient_email)->first();

        // Check if sender has enough credits
        if (!$sender->hasCredits($request->credits)) {
            return back()->withErrors(['credits' => 'Insufficient credits for transfer.']);
        }

        // Check if not transferring to self
        if ($sender->id === $recipient->id) {
            return back()->withErrors(['recipient_email' => 'Cannot transfer credits to yourself.']);
        }

        try {
            \DB::beginTransaction();

            // Deduct from sender
            $sender->deductCredits(
                $request->credits,
                "Transfer to {$recipient->name}" . ($request->message ? ": {$request->message}" : ''),
                'transfer',
                $recipient->id
            );

            // Add to recipient
            $recipient->addCredits(
                $request->credits,
                "Transfer from {$sender->name}" . ($request->message ? ": {$request->message}" : ''),
                'transfer',
                'credit_transfer',
                $sender->id
            );

            \DB::commit();

            return redirect()->route('credits.index')
                ->with('success', "Successfully transferred {$request->credits} credits to {$recipient->name}");

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => 'Transfer failed. Please try again.']);
        }
    }
}
