@extends('layouts.app')

@section('title', 'Chat with ' . $thread->aiAgent->name)

@section('content')
<div class="flex h-screen bg-gray-50 dark:bg-gray-900 -mt-16 -mb-8">
    <!-- Chat Container -->
    <div class="flex-1 flex flex-col max-w-4xl mx-auto w-full">
        <!-- Chat Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <!-- Back Button -->
                <a href="{{ route('chat.index') }}" 
                   class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mr-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Chats
                </a>
                
                <!-- Agent Info -->
                <div class="flex items-center">
                    @if($thread->aiAgent->avatar)
                        <img src="{{ Storage::url($thread->aiAgent->avatar) }}" 
                             alt="{{ $thread->aiAgent->name }}" 
                             class="w-10 h-10 rounded-full object-cover mr-3">
                    @else
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-robot text-purple-600 dark:text-purple-400"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">{{ $thread->aiAgent->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $thread->aiAgent->model }}</p>
                    </div>
                </div>
            </div>

            <!-- Chat Actions -->
            <div class="flex items-center space-x-3">
                <!-- Share Button -->
                @if($thread->user_id === auth()->id())
                    <button id="share-chat" 
                            class="inline-flex items-center px-3 py-1.5 text-sm bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200">
                        <i class="fas fa-share mr-1"></i>
                        Share
                    </button>
                @endif

                <!-- Export Button -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-download mr-1"></i>
                        Export
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    
                    <div x-show="open" 
                         @click.away="open = false"
                         x-transition
                         class="absolute right-0 mt-2 w-32 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-10">
                        <div class="py-1">
                            <a href="{{ route('chat.export', [$thread, 'txt']) }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-file-alt mr-2"></i>
                                Text
                            </a>
                            <a href="{{ route('chat.export', [$thread, 'json']) }}" 
                               class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <i class="fas fa-code mr-2"></i>
                                JSON
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Delete Chat -->
                @if($thread->user_id === auth()->id())
                    <form action="{{ route('chat.destroy', $thread) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this chat?')"
                          class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center px-3 py-1.5 text-sm bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors duration-200">
                            <i class="fas fa-trash mr-1"></i>
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Messages Container -->
        <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-6">
            <!-- Welcome Message -->
            @if($thread->aiAgent->welcome_message && $messages->isEmpty())
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        @if($thread->aiAgent->avatar)
                            <img src="{{ Storage::url($thread->aiAgent->avatar) }}" 
                                 alt="{{ $thread->aiAgent->name }}" 
                                 class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-sm"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 max-w-3xl">
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl px-4 py-3">
                            <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $thread->aiAgent->welcome_message }}</p>
                        </div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $thread->aiAgent->name }} • Welcome message
                        </div>
                    </div>
                </div>
            @endif

            <!-- Chat Messages -->
            @foreach($messages as $message)
                <div class="flex items-start space-x-3 {{ $message->role === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                    <!-- Avatar -->
                    <div class="flex-shrink-0">
                        @if($message->role === 'user')
                            @if(auth()->user()->avatar ?? false)
                                <img src="{{ Storage::url(auth()->user()->avatar) }}" 
                                     alt="You" 
                                     class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                            @endif
                        @else
                            @if($thread->aiAgent->avatar)
                                <img src="{{ Storage::url($thread->aiAgent->avatar) }}" 
                                     alt="{{ $thread->aiAgent->name }}" 
                                     class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-sm"></i>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Message Content -->
                    <div class="flex-1 max-w-3xl">
                        <div class="rounded-2xl px-4 py-3 {{ $message->role === 'user' ? 'bg-purple-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' }}">
                            <p class="whitespace-pre-wrap">{{ $message->content }}</p>
                        </div>
                        
                        <!-- Message Meta -->
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 {{ $message->role === 'user' ? 'text-right' : '' }}">
                            {{ $message->role === 'user' ? 'You' : $thread->aiAgent->name }} • 
                            {{ $message->created_at->format('M j, g:i A') }}
                            @if($message->tokens_used)
                                • {{ number_format($message->tokens_used) }} tokens
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Typing Indicator (Hidden by default) -->
            <div id="typing-indicator" class="flex items-start space-x-3 hidden">
                <div class="flex-shrink-0">
                    @if($thread->aiAgent->avatar)
                        <img src="{{ Storage::url($thread->aiAgent->avatar) }}" 
                             alt="{{ $thread->aiAgent->name }}" 
                             class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-sm"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 max-w-3xl">
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-2xl px-4 py-3">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                    <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        {{ $thread->aiAgent->name }} is typing...
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Input -->
        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-6">
            <!-- Credit Warning -->
            @if(!$canSendMessage)
                <div class="mb-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400 mr-3"></i>
                        <div>
                            <p class="text-sm text-yellow-800 dark:text-yellow-200 font-medium">
                                Insufficient Credits
                            </p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                You need credits to send messages. 
                                <a href="{{ route('credits.purchase') }}" class="font-medium underline hover:no-underline">
                                    Purchase credits
                                </a> to continue chatting.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form id="message-form" class="flex items-end space-x-4">
                @csrf
                <div class="flex-1">
                    <textarea id="message-input" 
                              name="message" 
                              rows="1" 
                              placeholder="{{ $canSendMessage ? 'Type your message...' : 'You need credits to send messages' }}"
                              {{ $canSendMessage ? '' : 'disabled' }}
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent resize-none {{ $canSendMessage ? '' : 'opacity-50 cursor-not-allowed' }}"
                              style="min-height: 52px; max-height: 200px;"></textarea>
                </div>
                
                <button type="submit" 
                        id="send-button"
                        {{ $canSendMessage ? '' : 'disabled' }}
                        class="inline-flex items-center justify-center w-12 h-12 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors duration-200 {{ $canSendMessage ? '' : 'opacity-50 cursor-not-allowed' }}">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <!-- Message Info -->
            <div class="mt-3 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <div>
                    Press Shift+Enter for new line, Enter to send
                </div>
                <div class="flex items-center space-x-4">
                    <span>Credits: <span id="user-credits">{{ auth()->user()->credits ?? 0 }}</span></span>
                    @if(auth()->user()->free_messages_used < auth()->user()->free_messages_limit)
                        <span>Free messages: {{ auth()->user()->free_messages_limit - auth()->user()->free_messages_used }} left</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div id="share-modal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black opacity-50"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Share Chat</h3>
                <button id="close-share-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <p class="text-gray-600 dark:text-gray-400">
                    Share this chat conversation with others. They'll be able to view the messages but not send new ones.
                </p>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Public Link
                    </label>
                    <div class="flex">
                        <input type="text" 
                               id="share-url" 
                               readonly 
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <button id="copy-share-url" 
                                class="px-4 py-2 bg-purple-600 text-white rounded-r-lg hover:bg-purple-700 transition-colors duration-200">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button id="cancel-share" 
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        Cancel
                    </button>
                    <button id="generate-share-link" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        Generate Link
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let isStreaming = false;

// Auto-resize textarea
const messageInput = document.getElementById('message-input');
messageInput.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = Math.min(this.scrollHeight, 200) + 'px';
});

// Handle Enter key
messageInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (!isStreaming && this.value.trim()) {
            document.getElementById('message-form').dispatchEvent(new Event('submit'));
        }
    }
});

// Handle form submission
document.getElementById('message-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    if (isStreaming || !{{ $canSendMessage ? 'true' : 'false' }}) return;
    
    const message = messageInput.value.trim();
    if (!message) return;
    
    // Disable form
    isStreaming = true;
    messageInput.disabled = true;
    document.getElementById('send-button').disabled = true;
    document.getElementById('send-button').innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    
    // Add user message to chat
    addMessageToChat(message, 'user');
    messageInput.value = '';
    messageInput.style.height = 'auto';
    
    // Show typing indicator
    showTypingIndicator();
    
    try {
        // Send message via streaming
        const response = await fetch('{{ route("chat.stream", $thread) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'text/event-stream',
            },
            body: JSON.stringify({ message: message })
        });
        
        if (!response.ok) {
            throw new Error('Failed to send message');
        }
        
        // Handle streaming response
        const reader = response.body.getReader();
        const decoder = new TextDecoder();
        let assistantMessage = '';
        let messageElement = null;
        
        hideTypingIndicator();
        
        while (true) {
            const { value, done } = await reader.read();
            if (done) break;
            
            const chunk = decoder.decode(value);
            const lines = chunk.split('\n');
            
            for (const line of lines) {
                if (line.startsWith('data: ')) {
                    const data = line.slice(6);
                    if (data === '[DONE]') {
                        break;
                    }
                    
                    try {
                        const parsed = JSON.parse(data);
                        if (parsed.content) {
                            assistantMessage += parsed.content;
                            
                            if (!messageElement) {
                                messageElement = addMessageToChat('', 'assistant');
                            }
                            
                            updateMessageContent(messageElement, assistantMessage);
                        }
                        
                        // Update credits
                        if (parsed.credits !== undefined) {
                            document.getElementById('user-credits').textContent = parsed.credits;
                        }
                    } catch (e) {
                        // Ignore parsing errors
                    }
                }
            }
        }
        
    } catch (error) {
        hideTypingIndicator();
        alert('Failed to send message: ' + error.message);
    } finally {
        // Re-enable form
        isStreaming = false;
        messageInput.disabled = false;
        document.getElementById('send-button').disabled = false;
        document.getElementById('send-button').innerHTML = '<i class="fas fa-paper-plane"></i>';
        messageInput.focus();
    }
});

function addMessageToChat(content, role) {
    const messagesContainer = document.getElementById('messages-container');
    const messageDiv = document.createElement('div');
    
    const isUser = role === 'user';
    const avatarHtml = isUser 
        ? '<div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center"><i class="fas fa-user text-blue-600 dark:text-blue-400 text-sm"></i></div>'
        : @if($thread->aiAgent->avatar)
            '<img src="{{ Storage::url($thread->aiAgent->avatar) }}" alt="{{ $thread->aiAgent->name }}" class="w-8 h-8 rounded-full object-cover">'
          @else
            '<div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center"><i class="fas fa-robot text-purple-600 dark:text-purple-400 text-sm"></i></div>'
          @endif;
    
    messageDiv.className = `flex items-start space-x-3 ${isUser ? 'flex-row-reverse space-x-reverse' : ''}`;
    messageDiv.innerHTML = `
        <div class="flex-shrink-0">${avatarHtml}</div>
        <div class="flex-1 max-w-3xl">
            <div class="rounded-2xl px-4 py-3 ${isUser ? 'bg-purple-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white'}">
                <p class="whitespace-pre-wrap message-content">${content}</p>
            </div>
            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 ${isUser ? 'text-right' : ''}">
                ${isUser ? 'You' : '{{ $thread->aiAgent->name }}'} • Just now
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    return messageDiv;
}

function updateMessageContent(messageElement, content) {
    const contentElement = messageElement.querySelector('.message-content');
    contentElement.textContent = content;
    
    const messagesContainer = document.getElementById('messages-container');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function showTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    indicator.classList.remove('hidden');
    
    const messagesContainer = document.getElementById('messages-container');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function hideTypingIndicator() {
    const indicator = document.getElementById('typing-indicator');
    indicator.classList.add('hidden');
}

// Share functionality
document.getElementById('share-chat')?.addEventListener('click', function() {
    document.getElementById('share-modal').classList.remove('hidden');
});

document.getElementById('close-share-modal').addEventListener('click', function() {
    document.getElementById('share-modal').classList.add('hidden');
});

document.getElementById('cancel-share').addEventListener('click', function() {
    document.getElementById('share-modal').classList.add('hidden');
});

document.getElementById('generate-share-link').addEventListener('click', async function() {
    try {
        const response = await fetch('{{ route("chat.share", $thread) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        if (data.success) {
            document.getElementById('share-url').value = data.url;
            document.getElementById('generate-share-link').style.display = 'none';
        }
    } catch (error) {
        alert('Failed to generate share link');
    }
});

document.getElementById('copy-share-url').addEventListener('click', function() {
    const input = document.getElementById('share-url');
    input.select();
    document.execCommand('copy');
    
    this.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-copy"></i>';
    }, 2000);
});

// Auto-scroll to bottom on load
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Focus message input
    if ({{ $canSendMessage ? 'true' : 'false' }}) {
        messageInput.focus();
    }
});
</script>
@endpush
@endsection