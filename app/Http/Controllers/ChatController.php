<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    private OpenAIService $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    /**
     * Start a new chat with an AI agent
     */
    public function start(AiAgent $agent)
    {
        // Check if agent is accessible
        if (!$agent->is_active || (!$agent->is_public && $agent->created_by !== auth()->id())) {
            abort(403, 'This AI agent is not available.');
        }

        // Create new chat thread
        $thread = ChatThread::create([
            'uuid' => Str::uuid(),
            'ai_agent_id' => $agent->id,
            'user_id' => auth()->id(),
            'title' => 'New Chat',
            'last_activity_at' => now(),
        ]);

        return redirect()->route('chat.show', $thread);
    }

    /**
     * Display the chat interface
     */
    public function show(ChatThread $thread)
    {
        // Check access permissions
        if ($thread->user_id !== auth()->id() && !$thread->is_public) {
            abort(403);
        }

        $agent = $thread->aiAgent;
        $messages = $thread->messages()->orderBy('created_at')->get();

        // Check if user can send messages (credits or free messages)
        $canSendMessage = $this->canUserSendMessage();

        return view('chat.show', compact('thread', 'agent', 'messages', 'canSendMessage'));
    }

    /**
     * Send a message in the chat
     */
    public function sendMessage(Request $request, ChatThread $thread)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Check access permissions
        if ($thread->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth()->user();
        $agent = $thread->aiAgent;

        // Check if user can send message
        if (!$this->canUserSendMessage()) {
            return response()->json([
                'error' => 'Insufficient credits. Please purchase more credits to continue chatting.',
                'redirect' => route('credits.purchase')
            ], 402);
        }

        try {
            DB::beginTransaction();

            // Store user message
            $userMessage = ChatMessage::create([
                'chat_thread_id' => $thread->id,
                'role' => 'user',
                'content' => $request->message,
            ]);

            // Deduct credits or increment free message count
            $this->deductMessageCredit($user);

            // Generate AI response
            $response = $this->openAIService->generateResponse($agent, $thread, $request->message);

            if (!$response['success']) {
                DB::rollBack();
                return response()->json(['error' => 'Failed to generate response: ' . $response['error']], 500);
            }

            // Store AI response
            $aiMessage = ChatMessage::create([
                'chat_thread_id' => $thread->id,
                'role' => 'assistant',
                'content' => $response['message'],
                'tokens_used' => $response['usage']['total_tokens'],
                'prompt_tokens' => $response['usage']['prompt_tokens'],
                'completion_tokens' => $response['usage']['completion_tokens'],
                'cost' => $response['cost'],
                'model_used' => $agent->model,
            ]);

            // Update thread title if this is the first exchange
            if ($thread->message_count <= 2) {
                $title = $this->generateThreadTitle($request->message);
                $thread->update(['title' => $title]);
            }

            // Update thread statistics
            $thread->update([
                'total_tokens_used' => $thread->total_tokens_used + $response['usage']['total_tokens'],
                'last_activity_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'user_message' => [
                    'id' => $userMessage->id,
                    'content' => $userMessage->content,
                    'created_at' => $userMessage->created_at->toISOString(),
                ],
                'ai_message' => [
                    'id' => $aiMessage->id,
                    'content' => $aiMessage->content,
                    'created_at' => $aiMessage->created_at->toISOString(),
                ],
                'usage' => $response['usage'],
                'remaining_credits' => $user->fresh()->credits,
                'can_send_message' => $this->canUserSendMessage(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Stream AI response (for real-time typing effect)
     */
    public function streamMessage(Request $request, ChatThread $thread)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Check access permissions
        if ($thread->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$this->canUserSendMessage()) {
            return response()->json([
                'error' => 'Insufficient credits',
                'redirect' => route('credits.purchase')
            ], 402);
        }

        return response()->stream(function () use ($request, $thread) {
            $user = auth()->user();
            $agent = $thread->aiAgent;

            try {
                DB::beginTransaction();

                // Store user message
                $userMessage = ChatMessage::create([
                    'chat_thread_id' => $thread->id,
                    'role' => 'user',
                    'content' => $request->message,
                ]);

                // Deduct credits
                $this->deductMessageCredit($user);

                // Send user message
                echo "data: " . json_encode([
                    'type' => 'user_message',
                    'message' => [
                        'id' => $userMessage->id,
                        'content' => $userMessage->content,
                        'created_at' => $userMessage->created_at->toISOString(),
                    ]
                ]) . "\n\n";
                flush();

                // Generate streaming response
                $fullResponse = '';
                $response = $this->openAIService->generateResponse($agent, $thread, $request->message);

                if ($response['success']) {
                    $fullResponse = $response['message'];
                    
                    // Simulate streaming by sending chunks
                    $chunks = str_split($fullResponse, 10);
                    foreach ($chunks as $chunk) {
                        echo "data: " . json_encode([
                            'type' => 'ai_chunk',
                            'chunk' => $chunk
                        ]) . "\n\n";
                        flush();
                        usleep(50000); // 50ms delay for typing effect
                    }

                    // Store complete AI response
                    $aiMessage = ChatMessage::create([
                        'chat_thread_id' => $thread->id,
                        'role' => 'assistant',
                        'content' => $fullResponse,
                        'tokens_used' => $response['usage']['total_tokens'],
                        'prompt_tokens' => $response['usage']['prompt_tokens'],
                        'completion_tokens' => $response['usage']['completion_tokens'],
                        'cost' => $response['cost'],
                        'model_used' => $agent->model,
                    ]);

                    // Send completion message
                    echo "data: " . json_encode([
                        'type' => 'ai_complete',
                        'message' => [
                            'id' => $aiMessage->id,
                            'content' => $fullResponse,
                            'created_at' => $aiMessage->created_at->toISOString(),
                        ],
                        'usage' => $response['usage'],
                        'remaining_credits' => $user->fresh()->credits,
                    ]) . "\n\n";
                    flush();

                    DB::commit();
                }

            } catch (\Exception $e) {
                DB::rollBack();
                echo "data: " . json_encode([
                    'type' => 'error',
                    'message' => $e->getMessage()
                ]) . "\n\n";
                flush();
            }

            echo "data: " . json_encode(['type' => 'done']) . "\n\n";
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    /**
     * Get user's chat threads
     */
    public function index()
    {
        $threads = auth()->user()->chatThreads()
            ->with('aiAgent')
            ->latest('last_activity_at')
            ->paginate(20);

        return view('chat.index', compact('threads'));
    }

    /**
     * Delete a chat thread
     */
    public function destroy(ChatThread $thread)
    {
        if ($thread->user_id !== auth()->id()) {
            abort(403);
        }

        $thread->delete();

        return redirect()->route('chat.index')
            ->with('success', 'Chat deleted successfully.');
    }

    /**
     * Share a chat thread publicly
     */
    public function share(ChatThread $thread)
    {
        if ($thread->user_id !== auth()->id()) {
            abort(403);
        }

        $thread->update([
            'is_public' => true,
            'public_token' => Str::random(32),
        ]);

        return response()->json([
            'success' => true,
            'share_url' => route('chat.public', $thread->public_token),
        ]);
    }

    /**
     * View a public chat
     */
    public function public(string $token)
    {
        $thread = ChatThread::where('public_token', $token)
            ->where('is_public', true)
            ->with('aiAgent', 'messages')
            ->firstOrFail();

        $agent = $thread->aiAgent;
        $messages = $thread->messages()->orderBy('created_at')->get();

        return view('chat.public', compact('thread', 'agent', 'messages'));
    }

    /**
     * Export chat as text/JSON
     */
    public function export(ChatThread $thread, string $format = 'txt')
    {
        if ($thread->user_id !== auth()->id()) {
            abort(403);
        }

        $messages = $thread->messages()->orderBy('created_at')->get();
        
        if ($format === 'json') {
            return response()->json([
                'thread' => $thread->toArray(),
                'agent' => $thread->aiAgent->toArray(),
                'messages' => $messages->toArray(),
            ])->header('Content-Disposition', 'attachment; filename="chat-' . $thread->uuid . '.json"');
        }

        // Text format
        $content = "Chat with {$thread->aiAgent->name}\n";
        $content .= "Date: " . $thread->created_at->format('Y-m-d H:i:s') . "\n\n";

        foreach ($messages as $message) {
            $role = ucfirst($message->role);
            if ($message->role === 'assistant') {
                $role = $thread->aiAgent->name;
            }
            $content .= "{$role}: {$message->content}\n\n";
        }

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="chat-' . $thread->uuid . '.txt"');
    }

    private function canUserSendMessage(): bool
    {
        $user = auth()->user();
        
        if (!$user) {
            return false;
        }

        // Check if user has credits
        if ($user->hasCredits()) {
            return true;
        }

        // Check if user can send free messages
        return $user->canSendFreeMessage();
    }

    private function deductMessageCredit($user): void
    {
        if ($user->hasCredits()) {
            // Deduct from credits
            $user->deductCredits(1, 'AI chat message', 'chat_message');
        } else {
            // Use free message
            $user->increment('free_messages_used');
        }
    }

    private function generateThreadTitle(string $firstMessage): string
    {
        // Generate a short title from the first message
        $title = Str::limit($firstMessage, 40, '...');
        
        // Remove line breaks and extra spaces
        $title = preg_replace('/\s+/', ' ', $title);
        
        return trim($title) ?: 'New Chat';
    }
}
