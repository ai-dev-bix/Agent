<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;

// Installation Routes (only accessible if not installed)
Route::middleware(['installation'])
    ->withoutMiddleware([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    ])->group(function () {
    Route::get('/install', [InstallationController::class, 'index'])->name('installation.welcome');
    Route::get('/install/requirements', [InstallationController::class, 'requirements'])->name('installation.requirements');
    Route::get('/install/database', [InstallationController::class, 'database'])->name('installation.database');
    Route::post('/install/database/test', [InstallationController::class, 'testDatabase'])->name('installation.database.test');
    Route::post('/install/database', [InstallationController::class, 'saveDatabase'])->name('installation.database.save');
    Route::get('/install/admin', [InstallationController::class, 'admin'])->name('installation.admin');
    Route::post('/install/admin', [InstallationController::class, 'saveAdmin'])->name('installation.admin.save');
    Route::get('/install/configuration', [InstallationController::class, 'configuration'])->name('installation.configuration');
    Route::post('/install/configuration', [InstallationController::class, 'saveConfiguration'])->name('installation.configuration.save');
    Route::get('/install/finalize', [InstallationController::class, 'finalize'])->name('installation.finalize');
    Route::post('/install/finalize', [InstallationController::class, 'install'])->name('installation.install');
});

// Redirect to installation if not completed
Route::get('/', function () {
    if (!env('INSTALLATION_COMPLETED', false)) {
        return redirect()->route('installation.welcome');
    }
    
    // Show public agents on homepage (skip if tables are not migrated)
    try {
        $agents = \App\Models\AiAgent::public()
            ->active()
            ->with('creator')
            ->latest()
            ->limit(6)
            ->get();
    } catch (\Throwable $e) {
        $agents = collect();
    }
    
    return view('welcome', compact('agents'));
})->name('home');

// Authentication Routes (Laravel Breeze)
require __DIR__.'/auth.php';

// OAuth Authentication Routes
Route::get('/auth/google', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/facebook', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleFacebookCallback'])->name('auth.facebook.callback');

// Public Routes
Route::get('/explore', [AiAgentController::class, 'public'])->name('agents.public');
Route::get('/chat/public/{token}', [ChatController::class, 'public'])->name('chat.public');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Dashboard
    Route::get('/dashboard', function () {
        $user = auth()->user();
        $recentChats = $user->chatThreads()
            ->with('aiAgent')
            ->latest('last_activity_at')
            ->limit(5)
            ->get();
        
        $userAgents = $user->aiAgents()->limit(3)->get();
        
        return view('dashboard', compact('user', 'recentChats', 'userAgents'));
    })->name('dashboard');

    // AI Agents Management
    Route::resource('agents', AiAgentController::class);
    Route::post('/agents/{agent}/clone', [AiAgentController::class, 'clone'])->name('agents.clone');

    // Chat System
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{thread}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{agent}/start', [ChatController::class, 'start'])->name('chat.start');
    Route::post('/chat/{thread}/message', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/{thread}/stream', [ChatController::class, 'streamMessage'])->name('chat.stream');
    Route::delete('/chat/{thread}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::post('/chat/{thread}/share', [ChatController::class, 'share'])->name('chat.share');
    Route::get('/chat/{thread}/export/{format?}', [ChatController::class, 'export'])->name('chat.export');

    // Credits System
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
    Route::get('/credits/purchase', [CreditController::class, 'purchase'])->name('credits.purchase');
    Route::get('/credits/history', [CreditController::class, 'history'])->name('credits.history');
    Route::get('/credits/usage', [CreditController::class, 'usage'])->name('credits.usage');
    Route::get('/api/credits/balance', [CreditController::class, 'balance'])->name('credits.balance');
    Route::post('/credits/transfer', [CreditController::class, 'transfer'])->name('credits.transfer');
    Route::post('/credits/gift', [CreditController::class, 'gift'])->name('credits.gift');

    // Payment System
    Route::post('/payments/stripe', [PaymentController::class, 'createStripePayment'])->name('payments.stripe');
    Route::post('/payments/paypal', [PaymentController::class, 'createPayPalPayment'])->name('payments.paypal');
    Route::post('/payments/paypal/success', [PaymentController::class, 'paypalSuccess'])->name('payments.paypal.success');
    Route::post('/payments/bank-transfer', [PaymentController::class, 'createBankTransfer'])->name('payments.bank-transfer');
    Route::get('/payments/{payment}/bank-transfer-instructions', [PaymentController::class, 'bankTransferInstructions'])->name('payments.bank-transfer-instructions');
    Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
});

// Webhook Routes (no auth required)
Route::post('/webhooks/stripe', [PaymentController::class, 'stripeWebhook'])->name('webhooks.stripe');

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    
    // AI Agents Management
    Route::get('/agents', [AdminController::class, 'agents'])->name('agents.index');
    Route::get('/agents/{agent}', [AdminController::class, 'showAgent'])->name('agents.show');
    
    // Payments Management
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminController::class, 'showPayment'])->name('payments.show');
    Route::post('/payments/{payment}/approve', [AdminController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{payment}/reject', [AdminController::class, 'rejectPayment'])->name('payments.reject');
    
    // Credit Packages Management
    Route::get('/credit-packages', [AdminController::class, 'creditPackages'])->name('credit-packages.index');
    Route::get('/credit-packages/create', [AdminController::class, 'createCreditPackage'])->name('credit-packages.create');
    Route::post('/credit-packages', [AdminController::class, 'storeCreditPackage'])->name('credit-packages.store');
    Route::get('/credit-packages/{package}/edit', [AdminController::class, 'editCreditPackage'])->name('credit-packages.edit');
    Route::put('/credit-packages/{package}', [AdminController::class, 'updateCreditPackage'])->name('credit-packages.update');
    
    // Analytics
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});

// SEO-friendly public agent pages
Route::get('/agent/{agent}', [AiAgentController::class, 'show'])->name('agents.public.show');

// Iframe embedding routes
Route::get('/embed/{agent}', function(\App\Models\AiAgent $agent) {
    if (!$agent->is_public || !$agent->is_active) {
        abort(404);
    }
    
    return view('embed.agent', compact('agent'));
})->name('embed.agent');

Route::get('/embed/{agent}/chat', function(\App\Models\AiAgent $agent) {
    if (!$agent->is_public || !$agent->is_active) {
        abort(404);
    }
    
    return view('embed.chat', compact('agent'));
})->name('embed.chat');
