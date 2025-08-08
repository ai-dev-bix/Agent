@extends('installation.layout')

@section('title', 'Installation Summary')
@section('step', 5)

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Ready to Install
    </h2>
    <p class="text-gray-600 dark:text-gray-300">
        Review your configuration and start the installation process. This will set up your database, create the admin user, and prepare your AI Agents SaaS platform.
    </p>
</div>

<div class="space-y-6">
    <!-- Installation Summary -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Installation Summary</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Database Configuration -->
            <div class="space-y-3">
                <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-database text-blue-600 dark:text-blue-400 mr-2"></i>
                    Database Configuration
                </h4>
                <div class="pl-6 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <p><span class="font-medium">Host:</span> {{ session('db_host', 'Not configured') }}</p>
                    <p><span class="font-medium">Database:</span> {{ session('db_database', 'Not configured') }}</p>
                    <p><span class="font-medium">Username:</span> {{ session('db_username', 'Not configured') }}</p>
                    <p><span class="font-medium">Status:</span> 
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            <i class="fas fa-check mr-1"></i>
                            Connected
                        </span>
                    </p>
                </div>
            </div>

            <!-- Admin User -->
            <div class="space-y-3">
                <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-user-shield text-purple-600 dark:text-purple-400 mr-2"></i>
                    Administrator Account
                </h4>
                <div class="pl-6 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <p><span class="font-medium">Name:</span> {{ session('admin_name', 'Not configured') }}</p>
                    <p><span class="font-medium">Email:</span> {{ session('admin_email', 'Not configured') }}</p>
                    <p><span class="font-medium">Password:</span> ••••••••</p>
                </div>
            </div>

            <!-- Application Settings -->
            <div class="space-y-3">
                <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-cog text-gray-600 dark:text-gray-400 mr-2"></i>
                    Application Settings
                </h4>
                <div class="pl-6 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <p><span class="font-medium">Name:</span> {{ session('app_name', 'AI Agents SaaS') }}</p>
                    <p><span class="font-medium">URL:</span> {{ session('app_url', request()->getSchemeAndHttpHost()) }}</p>
                    <p><span class="font-medium">Free Messages:</span> {{ session('default_free_messages', '3') }} per user</p>
                    <p><span class="font-medium">Credits per Message:</span> {{ session('default_credits_per_message', '1') }}</p>
                </div>
            </div>

            <!-- API Configuration -->
            <div class="space-y-3">
                <h4 class="font-medium text-gray-900 dark:text-white flex items-center">
                    <i class="fas fa-key text-yellow-600 dark:text-yellow-400 mr-2"></i>
                    API Configuration
                </h4>
                <div class="pl-6 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <p><span class="font-medium">OpenAI API:</span> 
                        @if(session('openai_api_key'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <i class="fas fa-check mr-1"></i>
                                Configured
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                <i class="fas fa-times mr-1"></i>
                                Not configured
                            </span>
                        @endif
                    </p>
                    <p><span class="font-medium">Stripe:</span> 
                        @if(session('stripe_key') && session('stripe_secret'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <i class="fas fa-check mr-1"></i>
                                Configured
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                <i class="fas fa-minus mr-1"></i>
                                Optional
                            </span>
                        @endif
                    </p>
                    <p><span class="font-medium">Google OAuth:</span> 
                        @if(session('google_client_id') && session('google_client_secret'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <i class="fas fa-check mr-1"></i>
                                Configured
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                <i class="fas fa-minus mr-1"></i>
                                Optional
                            </span>
                        @endif
                    </p>
                    <p><span class="font-medium">Email:</span> 
                        @if(session('mail_username') && session('mail_password'))
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <i class="fas fa-check mr-1"></i>
                                Configured
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                <i class="fas fa-minus mr-1"></i>
                                Optional
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Installation Steps Preview -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Installation Process</h3>
        
        <div class="space-y-4">
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mr-3 text-xs font-medium">
                    1
                </div>
                <span>Generate application encryption key</span>
            </div>
            
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mr-3 text-xs font-medium">
                    2
                </div>
                <span>Create database tables and structure</span>
            </div>
            
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mr-3 text-xs font-medium">
                    3
                </div>
                <span>Create administrator user account</span>
            </div>
            
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mr-3 text-xs font-medium">
                    4
                </div>
                <span>Set up default credit packages</span>
            </div>
            
            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mr-3 text-xs font-medium">
                    5
                </div>
                <span>Optimize and cache configuration</span>
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                    Important Notes
                </h3>
                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                    <ul class="list-disc list-inside space-y-1">
                        <li>This process may take a few minutes to complete</li>
                        <li>Do not close your browser or navigate away during installation</li>
                        <li>You can configure additional settings after installation</li>
                        <li>Make sure your OpenAI API key is valid for AI functionality</li>
                        <li>Assets are auto-loaded via CDN if a build is not present; no SSH is required</li>
                        <li>Public storage is linked automatically; if symlinks are not allowed, files are copied</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Installation Progress (Hidden by default) -->
    <div id="installation-progress" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hidden">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Installing...</h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-400">Overall Progress</span>
                <span class="text-sm font-medium text-gray-900 dark:text-white" id="progress-percentage">0%</span>
            </div>
            
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" id="progress-bar" style="width: 0%"></div>
            </div>
            
            <div class="text-sm text-gray-600 dark:text-gray-400" id="current-step">
                Preparing installation...
            </div>
        </div>
    </div>

    <!-- Installation Complete (Hidden by default) -->
    <div id="installation-complete" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-6 hidden">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-2xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-medium text-green-800 dark:text-green-200">
                    Installation Complete!
                </h3>
                <p class="mt-1 text-sm text-green-700 dark:text-green-300">
                    Your AI Agents SaaS platform has been successfully installed and is ready to use.
                </p>
            </div>
        </div>
        
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="/" 
               class="inline-flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-home mr-2"></i>
                Go to Homepage
            </a>
            <a href="/login" 
               class="inline-flex items-center justify-center px-6 py-3 border border-green-600 text-green-600 dark:text-green-400 rounded-lg hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors duration-200">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Admin Login
            </a>
        </div>
    </div>
</div>

<div class="flex justify-between mt-8" id="navigation-buttons">
    <a href="{{ route('installation.configuration') }}" 
       class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
        <i class="fas fa-arrow-left mr-2"></i>
        Back
    </a>
    
    <button type="button" 
            id="install-button"
            class="inline-flex items-center px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 text-lg font-medium">
        <i class="fas fa-rocket mr-2"></i>
        Start Installation
    </button>
</div>

@push('scripts')
<script>
document.getElementById('install-button').addEventListener('click', function() {
    const button = this;
    const progressSection = document.getElementById('installation-progress');
    const completeSection = document.getElementById('installation-complete');
    const navigationButtons = document.getElementById('navigation-buttons');
    const progressBar = document.getElementById('progress-bar');
    const progressPercentage = document.getElementById('progress-percentage');
    const currentStep = document.getElementById('current-step');
    
    // Disable button and show progress
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Installing...';
    progressSection.classList.remove('hidden');
    navigationButtons.style.display = 'none';
    
    // Simulate installation steps
    const steps = [
        { text: 'Generating application key...', progress: 20 },
        { text: 'Creating database tables...', progress: 40 },
        { text: 'Setting up admin user...', progress: 60 },
        { text: 'Creating default packages...', progress: 80 },
        { text: 'Finalizing installation...', progress: 100 }
    ];
    
    let currentStepIndex = 0;
    
    function runInstallationStep() {
        if (currentStepIndex < steps.length) {
            const step = steps[currentStepIndex];
            currentStep.textContent = step.text;
            progressBar.style.width = step.progress + '%';
            progressPercentage.textContent = step.progress + '%';
            
            currentStepIndex++;
            
            // Simulate processing time
            setTimeout(runInstallationStep, 2000);
        } else {
            // Installation complete
            setTimeout(function() {
                // Make actual installation request
                fetch('{{ route("installation.install") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        progressSection.classList.add('hidden');
                        completeSection.classList.remove('hidden');
                    } else {
                        throw new Error(data.message || 'Installation failed');
                    }
                })
                .catch(error => {
                    alert('Installation failed: ' + error.message);
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-rocket mr-2"></i>Start Installation';
                    progressSection.classList.add('hidden');
                    navigationButtons.style.display = 'flex';
                });
            }, 1000);
        }
    }
    
    // Start the installation process
    setTimeout(runInstallationStep, 500);
});
</script>
@endpush
@endsection