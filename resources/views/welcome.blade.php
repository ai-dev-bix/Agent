@extends('layouts.app')

@section('title', 'AI Agents SaaS Platform - Create and Chat with Custom AI Agents')
@section('page-title', 'Welcome')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Hero Section -->
    <div class="text-center py-12 lg:py-20">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 dark:text-white mb-6">
                Create & Chat with 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-blue-600">
                    AI Agents
                </span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                Build custom AI chatbots with unique personalities, train them with your content, 
                and share them with the world. Experience the future of conversational AI.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('agents.create') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your Agent
                    </a>
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-rocket mr-2"></i>
                        Get Started Free
                    </a>
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center px-8 py-4 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-semibold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>
    
    <!-- Features Section -->
    <div class="py-16 bg-gray-50 dark:bg-gray-800 rounded-2xl mb-16">
        <div class="max-w-6xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-900 dark:text-white mb-12">
                Why Choose Our Platform?
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-robot text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Custom AI Agents</h3>
                    <p class="text-gray-600 dark:text-gray-300">Create unique AI personalities with custom prompts, tones, and behaviors tailored to your needs.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comments text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Real-time Chat</h3>
                    <p class="text-gray-600 dark:text-gray-300">Experience smooth, ChatGPT-like conversations with streaming responses and conversation history.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-share-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Easy Sharing</h3>
                    <p class="text-gray-600 dark:text-gray-300">Share your AI agents publicly or embed them in websites with simple iframe integration.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-coins text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Flexible Pricing</h3>
                    <p class="text-gray-600 dark:text-gray-300">Pay-as-you-go credit system with free messages to get started. No monthly subscriptions.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-cog text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Advanced Controls</h3>
                    <p class="text-gray-600 dark:text-gray-300">Fine-tune your AI with temperature, creativity, and response length controls.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-bar text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Analytics</h3>
                    <p class="text-gray-600 dark:text-gray-300">Track usage, performance, and user engagement with detailed analytics and reports.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Public Agents Section -->
    @if($agents->count() > 0)
        <div class="mb-16">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Featured AI Agents
                </h2>
                <a href="{{ route('agents.public') }}" 
                   class="inline-flex items-center text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium">
                    View All
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($agents as $agent)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-shadow duration-200">
                        <div class="flex items-center space-x-4 mb-4">
                            @if($agent->avatar)
                                <img src="{{ Storage::url($agent->avatar) }}" 
                                     alt="{{ $agent->name }}" 
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-robot text-white"></i>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $agent->name }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    by {{ $agent->creator->name }}
                                </p>
                            </div>
                        </div>
                        
                        @if($agent->description)
                            <p class="text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                                {{ $agent->description }}
                            </p>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                    {{ $agent->category ?? 'General' }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $agent->model }}
                                </span>
                            </div>
                            
                            <a href="{{ route('agents.public.show', $agent) }}" 
                               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Chat Now
                                <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
    
    <!-- CTA Section -->
    <div class="text-center py-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                Ready to Build Your AI Agent?
            </h2>
            <p class="text-xl text-purple-100 mb-8">
                Join thousands of creators building the future of conversational AI.
            </p>
            
            @guest
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-purple-600 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-rocket mr-2"></i>
                    Start Building Today
                </a>
            @else
                <a href="{{ route('agents.create') }}" 
                   class="inline-flex items-center px-8 py-4 bg-white text-purple-600 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>
                    Create Your First Agent
                </a>
            @endguest
        </div>
    </div>
</div>

@push('styles')
<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endpush
@endsection