@extends('layouts.app')

@section('title', 'My AI Agents')

@section('header-actions')
<a href="{{ route('agents.create') }}" 
   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
    <i class="fas fa-plus mr-2"></i>
    Create Agent
</a>
@endsection

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">My AI Agents</h1>
        <p class="text-gray-600 dark:text-gray-400">
            Create and manage your custom AI agents. Each agent can have unique personalities, knowledge, and capabilities.
        </p>
    </div>

    @if($agents->count() > 0)
        <!-- Agents Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($agents as $agent)
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-200">
                    <!-- Agent Header -->
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                @if($agent->avatar)
                                    <img src="{{ Storage::url($agent->avatar) }}" 
                                         alt="{{ $agent->name }}" 
                                         class="w-12 h-12 rounded-lg object-cover mr-3">
                                @else
                                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-xl"></i>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $agent->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $agent->model }}</p>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            @if($agent->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                    <i class="fas fa-pause-circle mr-1"></i>
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <!-- Description -->
                        @if($agent->description)
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                                {{ $agent->description }}
                            </p>
                        @endif

                        <!-- Agent Details -->
                        <div class="space-y-2 mb-4">
                            @if($agent->category)
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-tag mr-2"></i>
                                    <span>{{ $agent->category }}</span>
                                </div>
                            @endif
                            
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-globe mr-2"></i>
                                <span>{{ $agent->output_language ?? 'English' }}</span>
                            </div>
                            
                            @if($agent->is_public)
                                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-eye mr-2"></i>
                                    <span>Public</span>
                                </div>
                            @endif
                        </div>

                        <!-- Stats -->
                        <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <span>
                                <i class="fas fa-comments mr-1"></i>
                                {{ $agent->chat_threads_count ?? 0 }} chats
                            </span>
                            <span>{{ $agent->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('chat.start', $agent) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                    <i class="fas fa-comment mr-1"></i>
                                    Chat
                                </a>
                                
                                @if($agent->is_public)
                                    <a href="{{ route('agents.public.show', $agent->slug) }}" 
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                        <i class="fas fa-external-link-alt mr-1"></i>
                                        View
                                    </a>
                                @endif
                            </div>

                            <!-- Dropdown Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" 
                                        class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                
                                <div x-show="open" 
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                                    <div class="py-1">
                                        <a href="{{ route('agents.show', $agent) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <i class="fas fa-eye mr-2"></i>
                                            View Details
                                        </a>
                                        <a href="{{ route('agents.edit', $agent) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <i class="fas fa-edit mr-2"></i>
                                            Edit
                                        </a>
                                        <a href="{{ route('agents.clone', $agent) }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <i class="fas fa-clone mr-2"></i>
                                            Clone
                                        </a>
                                        <div class="border-t border-gray-200 dark:border-gray-700"></div>
                                        <form action="{{ route('agents.destroy', $agent) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this agent? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <i class="fas fa-trash mr-2"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($agents->hasPages())
            <div class="flex justify-center">
                {{ $agents->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No AI Agents Yet</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                Create your first AI agent to get started. You can customize its personality, knowledge, and capabilities.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('agents.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Create Your First Agent
                </a>
                <a href="{{ route('agents.public') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-search mr-2"></i>
                    Explore Public Agents
                </a>
            </div>
        </div>
    @endif
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