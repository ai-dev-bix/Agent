<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $agent->name }} - AI Agent</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', sans-serif;
        }
        
        .chat-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f8fafc;
        }
        
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        
        .message {
            max-width: 85%;
            word-wrap: break-word;
        }
        
        .message.user {
            align-self: flex-end;
            background: #8b5cf6;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 1rem 1rem 0.25rem 1rem;
        }
        
        .message.assistant {
            align-self: flex-start;
            background: white;
            color: #374151;
            padding: 0.75rem 1rem;
            border-radius: 1rem 1rem 1rem 0.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .typing-indicator {
            align-self: flex-start;
            background: white;
            padding: 0.75rem 1rem;
            border-radius: 1rem 1rem 1rem 0.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .typing-dots {
            display: flex;
            gap: 0.25rem;
        }
        
        .typing-dot {
            width: 0.5rem;
            height: 0.5rem;
            background: #9ca3af;
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }
        
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-0.5rem); }
        }
        
        .input-container {
            background: white;
            border-top: 1px solid #e5e7eb;
            padding: 1rem;
            display: flex;
            gap: 0.75rem;
            align-items: end;
        }
        
        .message-input {
            flex: 1;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            resize: none;
            max-height: 120px;
            min-height: 44px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .message-input:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        
        .send-button {
            background: #8b5cf6;
            color: white;
            border: none;
            border-radius: 0.75rem;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .send-button:hover:not(:disabled) {
            background: #7c3aed;
        }
        
        .send-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .agent-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .agent-avatar {
            width: 40px;
            height: 40px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3e8ff;
            color: #8b5cf6;
        }
        
        .agent-info h3 {
            font-weight: 600;
            color: #111827;
            margin: 0;
            font-size: 16px;
        }
        
        .agent-info p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
        
        .powered-by {
            position: absolute;
            bottom: 8px;
            right: 8px;
            font-size: 10px;
            color: #9ca3af;
            background: rgba(255, 255, 255, 0.9);
            padding: 2px 6px;
            border-radius: 4px;
        }
        
        .powered-by a {
            color: #8b5cf6;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Agent Header -->
        <div class="agent-header">
            <div class="agent-avatar">
                @if($agent->avatar)
                    <img src="{{ Storage::url($agent->avatar) }}" alt="{{ $agent->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem;">
                @else
                    <i class="fas fa-robot"></i>
                @endif
            </div>
            <div class="agent-info">
                <h3>{{ $agent->name }}</h3>
                <p>{{ $agent->description ?? 'AI Assistant' }}</p>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="messages-container" id="messages-container">
            <!-- Welcome Message -->
            @if($agent->welcome_message)
                <div class="message assistant">
                    {{ $agent->welcome_message }}
                </div>
            @endif
        </div>

        <!-- Input Container -->
        <div class="input-container">
            <textarea 
                id="message-input" 
                class="message-input" 
                placeholder="Type your message..."
                rows="1"></textarea>
            <button id="send-button" class="send-button">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
        
        <!-- Powered By -->
        <div class="powered-by">
            Powered by <a href="{{ config('app.url') }}" target="_blank">{{ config('app.name') }}</a>
        </div>
    </div>

    <script>
        let isStreaming = false;
        const messagesContainer = document.getElementById('messages-container');
        const messageInput = document.getElementById('message-input');
        const sendButton = document.getElementById('send-button');

        // Auto-resize textarea
        messageInput.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Handle Enter key
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (!isStreaming && this.value.trim()) {
                    sendMessage();
                }
            }
        });

        // Handle send button click
        sendButton.addEventListener('click', function() {
            if (!isStreaming && messageInput.value.trim()) {
                sendMessage();
            }
        });

        async function sendMessage() {
            const message = messageInput.value.trim();
            if (!message || isStreaming) return;

            // Disable input
            isStreaming = true;
            messageInput.disabled = true;
            sendButton.disabled = true;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            // Add user message
            addMessage(message, 'user');
            messageInput.value = '';
            messageInput.style.height = 'auto';

            // Show typing indicator
            const typingIndicator = showTypingIndicator();

            try {
                // Create or get chat thread
                const response = await fetch('{{ route("chat.start", $agent) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({ message: message, embed: true })
                });

                if (!response.ok) {
                    throw new Error('Failed to send message');
                }

                const data = await response.json();
                
                // Remove typing indicator
                hideTypingIndicator(typingIndicator);
                
                // Add assistant response
                if (data.response) {
                    addMessage(data.response, 'assistant');
                }

            } catch (error) {
                hideTypingIndicator(typingIndicator);
                addMessage('Sorry, I encountered an error. Please try again.', 'assistant');
            } finally {
                // Re-enable input
                isStreaming = false;
                messageInput.disabled = false;
                sendButton.disabled = false;
                sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                messageInput.focus();
            }
        }

        function addMessage(content, role) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${role}`;
            messageDiv.textContent = content;
            
            messagesContainer.appendChild(messageDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'typing-indicator';
            typingDiv.innerHTML = `
                <div class="typing-dots">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            `;
            
            messagesContainer.appendChild(typingDiv);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            
            return typingDiv;
        }

        function hideTypingIndicator(indicator) {
            if (indicator && indicator.parentNode) {
                indicator.parentNode.removeChild(indicator);
            }
        }

        // Auto-scroll to bottom on load
        document.addEventListener('DOMContentLoaded', function() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
            messageInput.focus();
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    </script>
</body>
</html>