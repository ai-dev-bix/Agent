@extends('installation.layout')

@section('title', 'Admin User Setup')
@section('step', 3)

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Create Admin User
    </h2>
    <p class="text-gray-600 dark:text-gray-300">
        Create the main administrator account for your AI Agents SaaS platform. This user will have full access to all features and settings.
    </p>
</div>

<form method="POST" action="{{ route('installation.admin.save') }}">
    @csrf
    <div class="space-y-6">
        <div>
            <label for="admin_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Full Name
            </label>
            <input type="text" 
                   id="admin_name" 
                   name="admin_name" 
                   value="{{ old('admin_name') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="John Doe"
                   required>
            @error('admin_name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="admin_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Email Address
            </label>
            <input type="email" 
                   id="admin_email" 
                   name="admin_email" 
                   value="{{ old('admin_email') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="admin@example.com"
                   required>
            @error('admin_email')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="admin_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Password
            </label>
            <input type="password" 
                   id="admin_password" 
                   name="admin_password" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Enter a strong password"
                   minlength="8"
                   required>
            @error('admin_password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Password must be at least 8 characters long
            </p>
        </div>

        <div>
            <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Confirm Password
            </label>
            <input type="password" 
                   id="admin_password_confirmation" 
                   name="admin_password_confirmation" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Confirm your password"
                   minlength="8"
                   required>
            @error('admin_password_confirmation')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Application Settings -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Application Settings
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Application Name
                    </label>
                    <input type="text" 
                           id="app_name" 
                           name="app_name" 
                           value="{{ old('app_name', 'AI Agents SaaS') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="AI Agents SaaS"
                           required>
                    @error('app_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="app_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Application URL
                    </label>
                    <input type="url" 
                           id="app_url" 
                           name="app_url" 
                           value="{{ old('app_url', request()->getSchemeAndHttpHost()) }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="https://yourdomain.com"
                           required>
                    @error('app_url')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="default_free_messages" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Free Messages per User
                        </label>
                        <input type="number" 
                               id="default_free_messages" 
                               name="default_free_messages" 
                               value="{{ old('default_free_messages', '3') }}" 
                               min="0" 
                               max="100"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               required>
                        @error('default_free_messages')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="default_credits_per_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Credits per Message
                        </label>
                        <input type="number" 
                               id="default_credits_per_message" 
                               name="default_credits_per_message" 
                               value="{{ old('default_credits_per_message', '1') }}" 
                               min="1" 
                               max="10"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               required>
                        @error('default_credits_per_message')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-shield-alt text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        Security Reminder
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Use a strong, unique password for the admin account</li>
                            <li>Consider enabling two-factor authentication after installation</li>
                            <li>Keep your admin credentials secure and don't share them</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between mt-8">
        <a href="{{ route('installation.database') }}" 
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

@push('scripts')
<script>
// Password confirmation validation
document.getElementById('admin_password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('admin_password').value;
    const confirmation = this.value;
    
    if (confirmation && password !== confirmation) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('admin_password').addEventListener('input', function() {
    const confirmation = document.getElementById('admin_password_confirmation');
    if (confirmation.value) {
        confirmation.dispatchEvent(new Event('input'));
    }
});
</script>
@endpush
@endsection