@extends('installation.layout')

@section('title', 'Database Configuration')
@section('step', 2)

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Database Configuration
    </h2>
    <p class="text-gray-600 dark:text-gray-300">
        Configure your MySQL database connection. Make sure your database server is running and accessible.
    </p>
</div>

<form id="database-form" method="POST" action="{{ route('installation.database.save') }}">
    @csrf
    <div class="space-y-6">
        <div>
            <label for="db_host" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Database Host
            </label>
            <input type="text" 
                   id="db_host" 
                   name="db_host" 
                   value="{{ old('db_host', 'localhost') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="localhost"
                   required>
            @error('db_host')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="db_port" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Database Port
            </label>
            <input type="number" 
                   id="db_port" 
                   name="db_port" 
                   value="{{ old('db_port', '3306') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="3306"
                   required>
            @error('db_port')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="db_database" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Database Name
            </label>
            <input type="text" 
                   id="db_database" 
                   name="db_database" 
                   value="{{ old('db_database') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="ai_agents_db"
                   required>
            @error('db_database')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="db_username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Database Username
            </label>
            <input type="text" 
                   id="db_username" 
                   name="db_username" 
                   value="{{ old('db_username') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="root"
                   required>
            @error('db_username')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="db_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Database Password
            </label>
            <input type="password" 
                   id="db_password" 
                   name="db_password" 
                   value="{{ old('db_password') }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Enter database password">
            @error('db_password')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Test Connection Button -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-blue-800 dark:text-blue-200 mb-3">
                        Test your database connection before proceeding to ensure everything is configured correctly.
                    </p>
                    <button type="button" 
                            id="test-connection"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plug mr-2"></i>
                        Test Connection
                    </button>
                </div>
            </div>
        </div>

        <!-- Connection Status -->
        <div id="connection-status" class="hidden"></div>
    </div>

    <div class="flex justify-between mt-8">
        <a href="{{ route('installation.requirements') }}" 
           class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Back
        </a>
        
        <button type="submit" 
                id="continue-btn"
                class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 disabled:bg-gray-400 disabled:cursor-not-allowed">
            Continue
            <i class="fas fa-arrow-right ml-2"></i>
        </button>
    </div>
</form>

@push('scripts')
<script>
document.getElementById('test-connection').addEventListener('click', function() {
    const btn = this;
    const statusDiv = document.getElementById('connection-status');
    const continueBtn = document.getElementById('continue-btn');
    
    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';
    btn.disabled = true;
    statusDiv.classList.add('hidden');
    
    // Get form data
    const formData = new FormData();
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    formData.append('db_host', document.getElementById('db_host').value);
    formData.append('db_port', document.getElementById('db_port').value);
    formData.append('db_database', document.getElementById('db_database').value);
    formData.append('db_username', document.getElementById('db_username').value);
    formData.append('db_password', document.getElementById('db_password').value);
    
    // Test connection
    fetch('{{ route("installation.database.test") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        statusDiv.classList.remove('hidden');
        
        if (data.success) {
            statusDiv.innerHTML = `
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                Database connection successful!
                            </p>
                            <p class="text-sm text-green-600 dark:text-green-300 mt-1">
                                Your database configuration is working correctly.
                            </p>
                        </div>
                    </div>
                </div>
            `;
            continueBtn.disabled = false;
        } else {
            statusDiv.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                Database connection failed!
                            </p>
                            <p class="text-sm text-red-600 dark:text-red-300 mt-1">
                                ${data.message || 'Please check your database credentials and try again.'}
                            </p>
                        </div>
                    </div>
                </div>
            `;
            continueBtn.disabled = true;
        }
    })
    .catch(error => {
        statusDiv.classList.remove('hidden');
        statusDiv.innerHTML = `
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-600 dark:text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            Connection test failed!
                        </p>
                        <p class="text-sm text-red-600 dark:text-red-300 mt-1">
                            An error occurred while testing the connection.
                        </p>
                    </div>
                </div>
            </div>
        `;
        continueBtn.disabled = true;
    })
    .finally(() => {
        btn.innerHTML = '<i class="fas fa-plug mr-2"></i>Test Connection';
        btn.disabled = false;
    });
});
</script>
@endpush
@endsection