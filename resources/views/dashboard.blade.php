@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('header-actions')
<a href="{{ route('agents.create') }}" 
   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
    <i class="fas fa-plus mr-2"></i>
    New Agent
</a>
@endsection

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Welcome back, {{ $user->name }}!</h2>
                <p class="text-purple-100">Ready to create amazing AI conversations?</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ $user->credits }}</div>
                <div class="text-sm text-purple-100">Credits Available</div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fas fa-robot text-purple-600 dark:text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">My Agents</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $userAgents->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-comments text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Chats</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $recentChats->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-coins text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Credits</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->credits }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <i class="fas fa-fire text-orange-600 dark:text-orange-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Free Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->free_messages_limit - $user->free_messages_used }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Chats -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Chats</h3>
                    <a href="{{ route('chat.index') }}" 
                       class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-sm font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($recentChats->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentChats as $chat)
                            <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                @if($chat->aiAgent->avatar)
                                    <img src="{{ Storage::url($chat->aiAgent->avatar) }}" 
                                         alt="{{ $chat->aiAgent->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-robot text-white text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $chat->title }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        with {{ $chat->aiAgent->name }} • {{ $chat->last_activity_at->diffForHumans() }}
                                    </p>
                                </div>
                                <a href="{{ route('chat.show', $chat) }}" 
                                   class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No chats yet</p>
                        <a href="{{ route('agents.public') }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            Explore Agents
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- My AI Agents -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My AI Agents</h3>
                    <a href="{{ route('agents.index') }}" 
                       class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-sm font-medium">
                        View All
                    </a>
                </div>
            </div>
            <div class="p-6">
                @if($userAgents->count() > 0)
                    <div class="space-y-4">
                        @foreach($userAgents as $agent)
                            <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                @if($agent->avatar)
                                    <img src="{{ Storage::url($agent->avatar) }}" 
                                         alt="{{ $agent->name }}" 
                                         class="w-10 h-10 rounded-full object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <i class="fas fa-robot text-white text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $agent->name }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $agent->category ?? 'General' }} • {{ $agent->is_public ? 'Public' : 'Private' }}
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('agents.edit', $agent) }}" 
                                       class="text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('agents.show', $agent) }}" 
                                       class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-robot text-gray-400 text-3xl mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400 mb-4">No agents created yet</p>
                        <a href="{{ route('agents.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>
                            Create Your First Agent
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('agents.create') }}" 
               class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-300 dark:hover:border-purple-700 transition-all duration-200">
                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg">
                    <i class="fas fa-plus text-purple-600 dark:text-purple-400"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Create Agent</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Build a new AI agent</p>
                </div>
            </a>

            <a href="{{ route('agents.public') }}" 
               class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-green-50 dark:hover:bg-green-900/20 hover:border-green-300 dark:hover:border-green-700 transition-all duration-200">
                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg">
                    <i class="fas fa-globe text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Explore Agents</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Discover public agents</p>
                </div>
            </a>

            <a href="{{ route('credits.purchase') }}" 
               class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-200">
                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg">
                    <i class="fas fa-coins text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">Buy Credits</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Purchase more credits</p>
                </div>
            </a>

            <a href="{{ route('chat.index') }}" 
               class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:border-orange-300 dark:hover:border-orange-700 transition-all duration-200">
                <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-lg">
                    <i class="fas fa-comments text-orange-600 dark:text-orange-400"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900 dark:text-white">My Chats</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View all conversations</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
