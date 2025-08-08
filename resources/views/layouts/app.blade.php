<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-bind:class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('description', 'AI Agents SaaS - Create and deploy custom AI chatbots powered by advanced language models')">
    
    @stack('meta')
    
    <title>@yield('title', config('app.name', 'AI Agents SaaS'))</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @env('testing')
        
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endenv
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white dark:bg-gray-900 transition-colors duration-200" 
      x-data="{ 
          sidebarOpen: false,
          credits: {{ auth()->check() ? auth()->user()->credits : 0 }},
          updateCredits() {
              if (window.location.pathname.includes('chat')) {
                  fetch('/api/credits/balance')
                      .then(response => response.json())
                      .then(data => this.credits = data.credits);
              }
          }
      }"
      x-init="setInterval(updateCredits, 30000)">
    
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-50 dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-200 ease-in-out lg:translate-x-0"
         x-bind:class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
        
        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-robot text-white text-sm"></i>
                </div>
                <span class="font-bold text-lg text-gray-900 dark:text-white">AI Agents</span>
            </a>
            <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            @auth
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700 text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-home w-5"></i>
                    <span>Dashboard</span>
                </a>
                
                <!-- My Agents -->
                <a href="{{ route('agents.index') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('agents.*') ? 'bg-gray-100 dark:bg-gray-700 text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-robot w-5"></i>
                    <span>My Agents</span>
                </a>
                
                <!-- Chats -->
                <a href="{{ route('chat.index') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('chat.*') ? 'bg-gray-100 dark:bg-gray-700 text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-comments w-5"></i>
                    <span>Chats</span>
                </a>
                
                <!-- Credits -->
                <a href="{{ route('credits.index') }}" 
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('credits.*') ? 'bg-gray-100 dark:bg-gray-700 text-purple-600 dark:text-purple-400' : '' }}">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-coins w-5"></i>
                        <span>Credits</span>
                    </div>
                    <span class="text-xs bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 px-2 py-1 rounded-full" x-text="credits"></span>
                </a>
                
                <!-- Payments -->
                <a href="{{ route('payments.history') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('payments.*') ? 'bg-gray-100 dark:bg-gray-700 text-purple-600 dark:text-purple-400' : '' }}">
                    <i class="fas fa-credit-card w-5"></i>
                    <span>Payments</span>
                </a>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                    <!-- Public Agents -->
                    <a href="{{ route('agents.public') }}" 
                       class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <i class="fas fa-globe w-5"></i>
                        <span>Explore Agents</span>
                    </a>
                </div>
                
                @if(auth()->user()->is_admin)
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider px-3 mb-2">Admin</div>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <i class="fas fa-tachometer-alt w-5"></i>
                            <span>Admin Panel</span>
                        </a>
                    </div>
                @endif
            @else
                <a href="{{ route('agents.public') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-globe w-5"></i>
                    <span>Explore Agents</span>
                </a>
                
                <a href="{{ route('login') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-sign-in-alt w-5"></i>
                    <span>Login</span>
                </a>
                
                <a href="{{ route('register') }}" 
                   class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-user-plus w-5"></i>
                    <span>Register</span>
                </a>
            @endauth
        </nav>
        
        <!-- Theme Toggle & User Menu -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <!-- Theme Toggle -->
            <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)" 
                    class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full mb-2">
                <i class="fas fa-moon w-5" x-show="!darkMode"></i>
                <i class="fas fa-sun w-5" x-show="darkMode"></i>
                <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
            </button>
            
            @auth
                <!-- User Info -->
                <div class="flex items-center space-x-3 px-3 py-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 w-full">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @endauth
        </div>
    </div>
    
    <!-- Mobile sidebar overlay -->
    <div x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>
    
    <!-- Main content -->
    <div class="lg:pl-64 min-h-screen">
        <!-- Top bar -->
        <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 py-3 lg:px-6">
            <div class="flex items-center justify-between">
                <!-- Mobile menu button -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Page title -->
                <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
                    @yield('page-title', 'Dashboard')
                </h1>
                
                <!-- Right side actions -->
                <div class="flex items-center space-x-4">
                    @yield('header-actions')
                </div>
            </div>
        </div>
        
        <!-- Page content -->
        <main class="p-4 lg:p-6">
            <!-- Flash messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 bg-red-50 dark:bg-red-900 border border-red-200 dark:border-red-700 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            @yield('content')
        </main>
    </div>
    
    @stack('scripts')
</body>
</html>
