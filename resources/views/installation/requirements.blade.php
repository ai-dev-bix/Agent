@extends('installation.layout')

@section('title', 'System Requirements')
@section('step', 1)

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        System Requirements Check
    </h2>
    <p class="text-gray-600 dark:text-gray-300">
        Let's verify that your server meets all the requirements for AI Agents SaaS.
    </p>
</div>

<div class="space-y-6">
    <!-- PHP Version -->
    <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="flex items-center">
            @if(version_compare(PHP_VERSION, '8.2', '>='))
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                </div>
            @else
                <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                </div>
            @endif
            <div>
                <h3 class="font-medium text-gray-900 dark:text-white">PHP Version</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">Current: {{ PHP_VERSION }} (Required: 8.2+)</p>
            </div>
        </div>
        @if(version_compare(PHP_VERSION, '8.2', '>='))
            <span class="text-green-600 dark:text-green-400 text-sm font-medium">Passed</span>
        @else
            <span class="text-red-600 dark:text-red-400 text-sm font-medium">Failed</span>
        @endif
    </div>

    <!-- PHP Extensions -->
    @php
        $extensions = [
            'curl' => 'cURL',
            'json' => 'JSON',
            'mbstring' => 'Multibyte String',
            'openssl' => 'OpenSSL',
            'pdo' => 'PDO',
            'pdo_mysql' => 'PDO MySQL',
            'xml' => 'XML',
            'zip' => 'ZIP',
            'gd' => 'GD',
            'redis' => 'Redis',
            'bcmath' => 'BCMath'
        ];
    @endphp

    @foreach($extensions as $ext => $name)
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                @if(extension_loaded($ext))
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                    </div>
                @else
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                    </div>
                @endif
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $name }} Extension</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Required for proper functionality</p>
                </div>
            </div>
            @if(extension_loaded($ext))
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">Installed</span>
            @else
                <span class="text-red-600 dark:text-red-400 text-sm font-medium">Missing</span>
            @endif
        </div>
    @endforeach

    <!-- Directory Permissions -->
    @php
        $directories = [
            storage_path() => 'storage/',
            storage_path('app') => 'storage/app/',
            storage_path('framework') => 'storage/framework/',
            storage_path('logs') => 'storage/logs/',
            base_path('bootstrap/cache') => 'bootstrap/cache/'
        ];
    @endphp

    @foreach($directories as $path => $name)
        <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                @if(is_writable($path))
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                    </div>
                @else
                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                    </div>
                @endif
                <div>
                    <h3 class="font-medium text-gray-900 dark:text-white">{{ $name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Directory must be writable</p>
                </div>
            </div>
            @if(is_writable($path))
                <span class="text-green-600 dark:text-green-400 text-sm font-medium">Writable</span>
            @else
                <span class="text-red-600 dark:text-red-400 text-sm font-medium">Not Writable</span>
            @endif
        </div>
    @endforeach
</div>

@php
    $allPassed = version_compare(PHP_VERSION, '8.2', '>=') &&
                 array_reduce(array_keys($extensions), function($carry, $ext) {
                     return $carry && extension_loaded($ext);
                 }, true) &&
                 array_reduce(array_keys($directories), function($carry, $path) {
                     return $carry && is_writable($path);
                 }, true);
@endphp

<div class="flex justify-between mt-8">
    <a href="{{ route('installation.welcome') }}" 
       class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
        <i class="fas fa-arrow-left mr-2"></i>
        Back
    </a>
    
    @if($allPassed)
        <a href="{{ route('installation.database') }}" 
           class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
            Continue
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    @else
        <div class="inline-flex items-center px-6 py-3 bg-gray-400 text-white rounded-lg cursor-not-allowed">
            Fix Requirements First
        </div>
    @endif
</div>
@endsection