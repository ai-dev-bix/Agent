@extends('installation.layout')

@section('title', 'API Configuration')
@section('step', 4)

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        API Keys & Configuration
    </h2>
    <p class="text-gray-600 dark:text-gray-300">
        Configure your external service API keys. These are essential for the platform to function properly.
    </p>
</div>

<form method="POST" action="{{ route('installation.configuration.save') }}">
    @csrf
    <div class="space-y-8">
        <!-- OpenAI Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-brain text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">OpenAI Configuration</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Required for AI agent functionality</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                        Required
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label for="openai_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        OpenAI API Key
                    </label>
                    <input type="password" 
                           id="openai_api_key" 
                           name="openai_api_key" 
                           value="{{ old('openai_api_key') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="sk-..."
                           required>
                    @error('openai_api_key')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank" class="text-purple-600 dark:text-purple-400 hover:underline">OpenAI Platform</a>
                    </p>
                </div>

                <div>
                    <label for="openai_organization" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Organization ID (Optional)
                    </label>
                    <input type="text" 
                           id="openai_organization" 
                           name="openai_organization" 
                           value="{{ old('openai_organization') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="org-...">
                    @error('openai_organization')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Stripe Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Stripe Payment Gateway</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">For credit card payments</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                        Optional
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label for="stripe_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stripe Publishable Key
                    </label>
                    <input type="text" 
                           id="stripe_key" 
                           name="stripe_key" 
                           value="{{ old('stripe_key') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="pk_...">
                    @error('stripe_key')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stripe_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stripe Secret Key
                    </label>
                    <input type="password" 
                           id="stripe_secret" 
                           name="stripe_secret" 
                           value="{{ old('stripe_secret') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="sk_...">
                    @error('stripe_secret')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stripe_webhook_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Stripe Webhook Secret
                    </label>
                    <input type="password" 
                           id="stripe_webhook_secret" 
                           name="stripe_webhook_secret" 
                           value="{{ old('stripe_webhook_secret') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="whsec_...">
                    @error('stripe_webhook_secret')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Webhook URL: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ request()->getSchemeAndHttpHost() }}/webhooks/stripe</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- Google OAuth Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mr-4">
                    <i class="fab fa-google text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Google OAuth</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">For Google login integration</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                        Optional
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <div>
                    <label for="google_client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Google Client ID
                    </label>
                    <input type="text" 
                           id="google_client_id" 
                           name="google_client_id" 
                           value="{{ old('google_client_id') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="123456789-...">
                    @error('google_client_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="google_client_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Google Client Secret
                    </label>
                    <input type="password" 
                           id="google_client_secret" 
                           name="google_client_secret" 
                           value="{{ old('google_client_secret') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="GOCSPX-...">
                    @error('google_client_secret')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Redirect URI: <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">{{ request()->getSchemeAndHttpHost() }}/auth/google/callback</code>
                    </p>
                </div>
            </div>
        </div>

        <!-- Mail Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-envelope text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Email Configuration</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">For sending notifications and password resets</p>
                </div>
                <div class="ml-auto">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                        Optional
                    </span>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="mail_mailer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mail Driver
                        </label>
                        <select id="mail_mailer" 
                                name="mail_mailer" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="smtp" {{ old('mail_mailer', 'smtp') == 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ old('mail_mailer') == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="log" {{ old('mail_mailer') == 'log' ? 'selected' : '' }}>Log (Testing)</option>
                        </select>
                    </div>

                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Mail Host
                        </label>
                        <input type="text" 
                               id="mail_host" 
                               name="mail_host" 
                               value="{{ old('mail_host', 'smtp.gmail.com') }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="smtp.gmail.com">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Port
                        </label>
                        <input type="number" 
                               id="mail_port" 
                               name="mail_port" 
                               value="{{ old('mail_port', '587') }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="587">
                    </div>

                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Username
                        </label>
                        <input type="text" 
                               id="mail_username" 
                               name="mail_username" 
                               value="{{ old('mail_username') }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="your-email@gmail.com">
                    </div>

                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Password
                        </label>
                        <input type="password" 
                               id="mail_password" 
                               name="mail_password" 
                               value="{{ old('mail_password') }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="App password">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Encryption
                        </label>
                        <select id="mail_encryption" 
                                name="mail_encryption" 
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="tls" {{ old('mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="" {{ old('mail_encryption') == '' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            From Address
                        </label>
                        <input type="email" 
                               id="mail_from_address" 
                               name="mail_from_address" 
                               value="{{ old('mail_from_address', 'noreply@' . request()->getHost()) }}" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="noreply@yourdomain.com">
                    </div>
                </div>
            </div>
        </div>

        <!-- Skip Notice -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        Configuration Notice
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>OpenAI API key is required for AI functionality</li>
                            <li>Optional services can be configured later in admin settings</li>
                            <li>You can skip optional configurations and set them up after installation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between mt-8">
        <a href="{{ route('installation.admin') }}" 
           class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
        
        <button type="submit" 
                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
            Continue
            <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </div>
</form>
@endsection