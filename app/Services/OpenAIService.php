<?php

namespace App\Services;

use OpenAI\Client;
use App\Models\AiAgent;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use Exception;

class OpenAIService
{
    private Client $client;

    public function __construct()
    {
        $this->client = \OpenAI::client(config('services.openai.api_key'));
    }

    public function generateResponse(AiAgent $agent, ChatThread $thread, string $userMessage): array
    {
        try {
            // Get conversation history
            $messages = $this->buildMessageHistory($agent, $thread, $userMessage);
            
            // Make API call to OpenAI
            $response = $this->client->chat()->create([
                'model' => $agent->model,
                'messages' => $messages,
                'temperature' => (float) $agent->temperature,
                'top_p' => (float) $agent->top_p,
                'frequency_penalty' => (float) $agent->frequency_penalty,
                'presence_penalty' => (float) $agent->presence_penalty,
                'max_tokens' => $agent->max_tokens,
            ]);

            $assistantMessage = $response->choices[0]->message->content;
            $usage = $response->usage;

            // Calculate cost (approximate pricing)
            $cost = $this->calculateCost($agent->model, $usage->promptTokens, $usage->completionTokens);

            return [
                'success' => true,
                'message' => $assistantMessage,
                'usage' => [
                    'prompt_tokens' => $usage->promptTokens,
                    'completion_tokens' => $usage->completionTokens,
                    'total_tokens' => $usage->totalTokens,
                ],
                'cost' => $cost,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    private function buildMessageHistory(AiAgent $agent, ChatThread $thread, string $userMessage): array
    {
        $messages = [];

        // Add system message
        $messages[] = [
            'role' => 'system',
            'content' => $agent->system_prompt,
        ];

        // Add conversation history (last 10 messages to stay within token limits)
        $recentMessages = $thread->messages()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->reverse();

        foreach ($recentMessages as $message) {
            if ($message->role !== 'system') {
                $messages[] = [
                    'role' => $message->role,
                    'content' => $message->content,
                ];
            }
        }

        // Add current user message
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
    }

    private function calculateCost(string $model, int $promptTokens, int $completionTokens): float
    {
        // Approximate pricing per 1K tokens (as of 2024)
        $pricing = [
            'gpt-4' => ['input' => 0.03, 'output' => 0.06],
            'gpt-4-turbo' => ['input' => 0.01, 'output' => 0.03],
            'gpt-3.5-turbo' => ['input' => 0.0015, 'output' => 0.002],
        ];

        $modelKey = $model;
        if (!isset($pricing[$modelKey])) {
            $modelKey = 'gpt-3.5-turbo'; // fallback
        }

        $inputCost = ($promptTokens / 1000) * $pricing[$modelKey]['input'];
        $outputCost = ($completionTokens / 1000) * $pricing[$modelKey]['output'];

        return $inputCost + $outputCost;
    }

    public function validateApiKey(string $apiKey = null): bool
    {
        try {
            $client = \OpenAI::client($apiKey ?? config('services.openai.api_key'));
            $client->models()->list();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-4' => 'GPT-4',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
        ];
    }
}