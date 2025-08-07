<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AiAgent;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use App\Models\Payment;
use App\Models\CreditPackage;
use App\Models\UserCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Admin dashboard overview
     */
    public function dashboard()
    {
        // Get key metrics
        $totalUsers = User::count();
        $totalAgents = AiAgent::count();
        $totalChats = ChatThread::count();
        $totalMessages = ChatMessage::count();
        $totalRevenue = Payment::completed()->sum('amount');
        $pendingPayments = Payment::pending()->count();

        // Recent activity
        $recentUsers = User::latest()->limit(5)->get();
        $recentPayments = Payment::with('user', 'creditPackage')->latest()->limit(5)->get();
        $topAgents = AiAgent::withCount('chatThreads')
            ->orderBy('chat_threads_count', 'desc')
            ->limit(5)
            ->get();

        // Monthly statistics
        $monthlyStats = $this->getMonthlyStats();
        $dailyStats = $this->getDailyStats();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalAgents', 
            'totalChats',
            'totalMessages',
            'totalRevenue',
            'pendingPayments',
            'recentUsers',
            'recentPayments',
            'topAgents',
            'monthlyStats',
            'dailyStats'
        ));
    }

    /**
     * User management
     */
    public function users()
    {
        $users = User::withCount(['aiAgents', 'chatThreads', 'payments'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function showUser(User $user)
    {
        $user->loadCount(['aiAgents', 'chatThreads', 'payments']);
        
        $recentChats = $user->chatThreads()
            ->with('aiAgent')
            ->latest('last_activity_at')
            ->limit(10)
            ->get();

        $creditHistory = $user->creditHistory()
            ->latest()
            ->limit(10)
            ->get();

        $paymentHistory = $user->payments()
            ->with('creditPackage')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.users.show', compact('user', 'recentChats', 'creditHistory', 'paymentHistory'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
            'credits' => 'required|integer|min:0',
            'free_messages_limit' => 'required|integer|min:0',
        ]);

        $oldCredits = $user->credits;
        $user->update($request->all());

        // Log credit adjustment if changed
        if ($oldCredits != $request->credits) {
            $difference = $request->credits - $oldCredits;
            $user->addCredits(
                abs($difference),
                'Admin adjustment',
                $difference > 0 ? 'adjustment' : 'adjustment',
                'admin_adjustment',
                auth()->id()
            );
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    /**
     * AI Agents management
     */
    public function agents()
    {
        $agents = AiAgent::with('creator')
            ->withCount('chatThreads')
            ->latest()
            ->paginate(20);

        return view('admin.agents.index', compact('agents'));
    }

    public function showAgent(AiAgent $agent)
    {
        $agent->load('creator');
        $agent->loadCount('chatThreads');
        
        $recentChats = $agent->chatThreads()
            ->with('user')
            ->latest('last_activity_at')
            ->limit(10)
            ->get();

        $usageStats = ChatMessage::whereHas('chatThread', function($q) use ($agent) {
            $q->where('ai_agent_id', $agent->id);
        })->selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->where('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return view('admin.agents.show', compact('agent', 'recentChats', 'usageStats'));
    }

    /**
     * Payments management
     */
    public function payments()
    {
        $payments = Payment::with('user', 'creditPackage')
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function showPayment(Payment $payment)
    {
        $payment->load('user', 'creditPackage');
        
        return view('admin.payments.show', compact('payment'));
    }

    public function approvePayment(Payment $payment)
    {
        if ($payment->payment_method !== 'bank_transfer' || $payment->status !== 'pending') {
            return back()->with('error', 'Payment cannot be approved.');
        }

        $payment->update([
            'status' => 'completed',
            'processed_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $payment->markAsCompleted();

        return back()->with('success', 'Payment approved and credits added to user account.');
    }

    public function rejectPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'failed',
            'notes' => $request->reason,
        ]);

        return back()->with('success', 'Payment rejected.');
    }

    /**
     * Credit packages management
     */
    public function creditPackages()
    {
        $packages = CreditPackage::withCount('payments')
            ->orderBy('sort_order')
            ->get();

        return view('admin.credit-packages.index', compact('packages'));
    }

    public function createCreditPackage()
    {
        return view('admin.credit-packages.create');
    }

    public function storeCreditPackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'bonus_credits' => 'required|integer|min:0',
            'is_popular' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'features' => 'nullable|array',
        ]);

        CreditPackage::create($request->all());

        return redirect()->route('admin.credit-packages.index')
            ->with('success', 'Credit package created successfully.');
    }

    public function editCreditPackage(CreditPackage $package)
    {
        return view('admin.credit-packages.edit', compact('package'));
    }

    public function updateCreditPackage(Request $request, CreditPackage $package)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'credits' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'bonus_credits' => 'required|integer|min:0',
            'is_popular' => 'boolean',
            'sort_order' => 'required|integer|min:0',
            'features' => 'nullable|array',
        ]);

        $package->update($request->all());

        return redirect()->route('admin.credit-packages.index')
            ->with('success', 'Credit package updated successfully.');
    }

    /**
     * Analytics
     */
    public function analytics()
    {
        // Revenue analytics
        $revenueData = $this->getRevenueAnalytics();
        $userGrowth = $this->getUserGrowthAnalytics();
        $usageAnalytics = $this->getUsageAnalytics();
        $topPerformers = $this->getTopPerformers();

        return view('admin.analytics', compact(
            'revenueData',
            'userGrowth', 
            'usageAnalytics',
            'topPerformers'
        ));
    }

    /**
     * System settings
     */
    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'default_free_messages' => 'required|integer|min:0',
            'default_credits_per_message' => 'required|integer|min:1',
        ]);

        // Update environment variables
        $this->updateEnvFile([
            'APP_NAME' => '"' . $request->app_name . '"',
            'DEFAULT_FREE_MESSAGES' => $request->default_free_messages,
            'DEFAULT_CREDITS_PER_MESSAGE' => $request->default_credits_per_message,
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }

    private function getMonthlyStats()
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Payment::completed()
                    ->whereYear('processed_at', $date->year)
                    ->whereMonth('processed_at', $date->month)
                    ->sum('amount'),
                'messages' => ChatMessage::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }
        return $months;
    }

    private function getDailyStats()
    {
        $days = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = [
                'date' => $date->format('M j'),
                'users' => User::whereDate('created_at', $date)->count(),
                'messages' => ChatMessage::whereDate('created_at', $date)->count(),
                'revenue' => Payment::completed()
                    ->whereDate('processed_at', $date)
                    ->sum('amount'),
            ];
        }
        return $days;
    }

    private function getRevenueAnalytics()
    {
        return [
            'total' => Payment::completed()->sum('amount'),
            'this_month' => Payment::completed()
                ->whereMonth('processed_at', now()->month)
                ->sum('amount'),
            'last_month' => Payment::completed()
                ->whereMonth('processed_at', now()->subMonth()->month)
                ->sum('amount'),
            'by_method' => Payment::completed()
                ->groupBy('payment_method')
                ->selectRaw('payment_method, SUM(amount) as total')
                ->get(),
        ];
    }

    private function getUserGrowthAnalytics()
    {
        return [
            'total' => User::count(),
            'this_month' => User::whereMonth('created_at', now()->month)->count(),
            'active_users' => User::whereHas('chatThreads', function($q) {
                $q->where('last_activity_at', '>=', now()->subDays(30));
            })->count(),
        ];
    }

    private function getUsageAnalytics()
    {
        return [
            'total_messages' => ChatMessage::count(),
            'total_threads' => ChatThread::count(),
            'avg_messages_per_thread' => ChatThread::withCount('messages')
                ->get()
                ->avg('messages_count'),
            'credits_used' => UserCredit::where('type', 'usage')->sum('credits') * -1,
        ];
    }

    private function getTopPerformers()
    {
        return [
            'agents' => AiAgent::withCount('chatThreads')
                ->orderBy('chat_threads_count', 'desc')
                ->limit(10)
                ->get(),
            'users' => User::withCount('chatThreads')
                ->orderBy('chat_threads_count', 'desc')
                ->limit(10)
                ->get(),
        ];
    }

    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);
        
        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }
        
        file_put_contents($envFile, $envContent);
    }
}
