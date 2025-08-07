@extends('layouts.app')

@section('title', $agent->name . ' - AI Agent')
@section('description', $agent->description ?? 'Chat with ' . $agent->name . ', an AI agent powered by ' . $agent->model)

@push('meta')
<!-- SEO Meta Tags -->
<meta name="keywords" content="AI agent, chatbot, {{ $agent->name }}, {{ $agent->category }}, artificial intelligence, {{ $agent->model }}">
<meta name="author" content="{{ $agent->creator->name }}">
<meta name="robots" content="index, follow">
<link rel="canonical" href="{{ route('agents.public.show', $agent->slug) }}">

<!-- Open Graph Meta Tags -->
<meta property="og:title" content="{{ $agent->name }} - AI Agent">
<meta property="og:description" content="{{ $agent->description ?? 'Chat with ' . $agent->name . ', an AI agent powered by ' . $agent->model }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ route('agents.public.show', $agent->slug) }}">
@if($agent->avatar)
<meta property="og:image" content="{{ Storage::url($agent->avatar) }}">
@endif
<meta property="og:site_name" content="{{ config('app.name') }}">

<!-- Twitter Card Meta Tags -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $agent->name }} - AI Agent">
<meta name="twitter:description" content="{{ $agent->description ?? 'Chat with ' . $agent->name . ', an AI agent powered by ' . $agent->model }}">
@if($agent->avatar)
<meta name="twitter:image" content="{{ Storage::url($agent->avatar) }}">
@endif

<!-- Structured Data (JSON-LD) -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "{{ $agent->name }}",
  "description": "{{ $agent->description ?? 'An AI agent powered by ' . $agent->model }}",
  "url": "{{ route('agents.public.show', $agent->slug) }}",
  "applicationCategory": "AI Assistant",
  "operatingSystem": "Web Browser",
  "offers": {
    "@type": "Offer",
    "price": "0",
    "priceCurrency": "USD"
  },
  "author": {
    "@type": "Person",
    "name": "{{ $agent->creator->name }}"
  },
  "dateCreated": "{{ $agent->created_at->toISOString() }}",
  "dateModified": "{{ $agent->updated_at->toISOString() }}",
  @if($agent->avatar)
  "image": "{{ Storage::url($agent->avatar) }}",
  @endif
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.5",
    "reviewCount": "{{ $agent->chat_threads_count ?? 0 }}"
  }
}
</script>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Agent Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 mb-8">
        <div class="flex flex-col md:flex-row items-start gap-6">
            <!-- Agent Avatar -->
            <div class="flex-shrink-0">
                @if($agent->avatar)
                    <img src="{{ Storage::url($agent->avatar) }}" 
                         alt="{{ $agent->name }}" 
                         class="w-24 h-24 rounded-xl object-cover">
                @else
                    <div class="w-24 h-24 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                        <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-3xl"></i>
                    </div>
                @endif
            </div>

            <!-- Agent Info -->
            <div class="flex-1">
                <div class="mb-4">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $agent->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <span class="flex items-center">
                            <i class="fas fa-user mr-1"></i>
                            Created by {{ $agent->creator->name }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ $agent->created_at->format('M j, Y') }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-comments mr-1"></i>
                            {{ number_format($agent->chat_threads_count ?? 0) }} chats
                        </span>
                    </div>
                </div>

                @if($agent->description)
                    <p class="text-gray-700 dark:text-gray-300 text-lg mb-6">{{ $agent->description }}</p>
                @endif

                <!-- Agent Details -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    @if($agent->category)
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-tag text-purple-600 dark:text-purple-400 mb-2"></i>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Category</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $agent->category }}</div>
                        </div>
                    @endif

                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-brain text-purple-600 dark:text-purple-400 mb-2"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">AI Model</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $agent->model }}</div>
                    </div>

                    <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <i class="fas fa-globe text-purple-600 dark:text-purple-400 mb-2"></i>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">Language</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">{{ $agent->output_language ?? 'English' }}</div>
                    </div>

                    @if($agent->tone)
                        <div class="text-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-volume-up text-purple-600 dark:text-purple-400 mb-2"></i>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">Tone</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ $agent->tone }}</div>
                        </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('chat.start', $agent) }}" 
                       class="inline-flex items-center justify-center px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-comment mr-2"></i>
                        Start Chatting
                    </a>
                    
                    <button onclick="copyEmbedCode()" 
                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-code mr-2"></i>
                        Embed Code
                    </button>
                    
                    <button onclick="shareAgent()" 
                            class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-share mr-2"></i>
                        Share
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent Capabilities -->
    @if($agent->system_prompt || $agent->welcome_message)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">About This Agent</h2>
            
            @if($agent->welcome_message)
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Welcome Message</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-900 dark:text-white">{{ $agent->welcome_message }}</p>
                    </div>
                </div>
            @endif

            @if($agent->system_prompt && strlen($agent->system_prompt) < 500)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Capabilities</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-900 dark:text-white">{{ $agent->system_prompt }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Technical Details -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Technical Specifications</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">AI Configuration</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Model:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $agent->model }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Temperature:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $agent->temperature ?? '0.7' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Max Tokens:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ number_format($agent->max_tokens ?? 1000) }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Response Style</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Language:</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ $agent->output_language ?? 'English' }}</span>
                    </div>
                    @if($agent->tone)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tone:</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $agent->tone }}</span>
                        </div>
                    @endif
                    @if($agent->writing_style)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Style:</span>
                            <span class="text-gray-900 dark:text-white font-medium">{{ $agent->writing_style }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Agents -->
    @if($similarAgents && $similarAgents->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Similar Agents</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($similarAgents as $similarAgent)
                    <a href="{{ route('agents.public.show', $similarAgent->slug) }}" 
                       class="block p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center mb-2">
                            @if($similarAgent->avatar)
                                <img src="{{ Storage::url($similarAgent->avatar) }}" 
                                     alt="{{ $similarAgent->name }}" 
                                     class="w-8 h-8 rounded-lg object-cover mr-3">
                            @else
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-sm"></i>
                                </div>
                            @endif
                            <div>
                                <h3 class="font-medium text-gray-900 dark:text-white text-sm">{{ $similarAgent->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $similarAgent->category }}</p>
                            </div>
                        </div>
                        @if($similarAgent->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ Str::limit($similarAgent->description, 100) }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

<!-- Embed Code Modal -->
<div id="embed-modal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Embed This Agent</h3>
                <button onclick="closeEmbedModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-400">
                    Copy and paste this code into your website to embed {{ $agent->name }}.
                </p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Embed Code (iframe)
                    </label>
                    <textarea id="embed-code" readonly 
                              class="w-full h-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-mono"
                              onclick="this.select()"><iframe src="{{ route('embed.agent', $agent) }}" width="100%" height="600" frameborder="0" title="{{ $agent->name }} - AI Agent"></iframe></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="closeEmbedModal()" 
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <button onclick="copyEmbedToClipboard()" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        Copy Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyEmbedCode() {
    document.getElementById('embed-modal').classList.remove('hidden');
}

function closeEmbedModal() {
    document.getElementById('embed-modal').classList.add('hidden');
}

function copyEmbedToClipboard() {
    const embedCode = document.getElementById('embed-code');
    embedCode.select();
    document.execCommand('copy');
    
    // Show feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-purple-600');
    
    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-purple-600');
    }, 2000);
}

function shareAgent() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $agent->name }} - AI Agent',
            text: '{{ $agent->description ?? "Check out this AI agent" }}',
            url: window.location.href
        });
    } else {
        // Fallback: copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('URL copied to clipboard!');
        });
    }
}
</script>
@endpush

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