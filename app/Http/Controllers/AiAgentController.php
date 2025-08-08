<?php

namespace App\Http\Controllers;

use App\Models\AiAgent;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AiAgentController extends Controller
{
    private OpenAIService $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->middleware('auth')->except(['public', 'show']);
        $this->openAIService = $openAIService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = auth()->user()->aiAgents()
            ->latest()
            ->paginate(12);

        return view('agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $models = $this->openAIService->getAvailableModels();
        
        return view('agents.create', compact('models'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'system_prompt' => 'required|string|max:4000',
            'model' => 'required|string|in:gpt-4,gpt-4-turbo,gpt-3.5-turbo',
            'temperature' => 'required|numeric|between:0,2',
            'top_p' => 'required|numeric|between:0,1',
            'frequency_penalty' => 'required|numeric|between:-2,2',
            'presence_penalty' => 'required|numeric|between:-2,2',
            'max_tokens' => 'required|integer|between:1,4000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'welcome_message' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'output_language' => 'required|string|max:10',
            'tone' => 'required|string|max:50',
            'writing_style' => 'required|string|max:50',
            'is_public' => 'boolean',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();
        $data['is_public'] = $request->has('is_public');

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Generate unique slug
        $data['slug'] = $this->generateUniqueSlug($request->name);

        $agent = AiAgent::create($data);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'AI Agent created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(AiAgent $agent)
    {
        // If guest, only allow public & active agents
        if (!auth()->check()) {
            if (!$agent->is_public || !$agent->is_active) {
                abort(404);
            }
        } else {
            // Check if user can view this agent
            if (!$agent->is_public && $agent->created_by !== auth()->id()) {
                abort(403);
            }
        }

        $recentChats = collect();
        if (auth()->check()) {
            $recentChats = $agent->chatThreads()
                ->where('user_id', auth()->id())
                ->latest('last_activity_at')
                ->limit(5)
                ->get();
        }

        return view('agents.show', compact('agent', 'recentChats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AiAgent $agent)
    {
        // Check if user can edit this agent
        if ($agent->created_by !== auth()->id()) {
            abort(403);
        }

        $models = $this->openAIService->getAvailableModels();
        
        return view('agents.edit', compact('agent', 'models'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AiAgent $agent)
    {
        // Check if user can edit this agent
        if ($agent->created_by !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'system_prompt' => 'required|string|max:4000',
            'model' => 'required|string|in:gpt-4,gpt-4-turbo,gpt-3.5-turbo',
            'temperature' => 'required|numeric|between:0,2',
            'top_p' => 'required|numeric|between:0,1',
            'frequency_penalty' => 'required|numeric|between:-2,2',
            'presence_penalty' => 'required|numeric|between:-2,2',
            'max_tokens' => 'required|integer|between:1,4000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'welcome_message' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
            'output_language' => 'required|string|max:10',
            'tone' => 'required|string|max:50',
            'writing_style' => 'required|string|max:50',
            'is_public' => 'boolean',
        ]);

        $data = $request->all();
        $data['is_public'] = $request->has('is_public');

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($agent->avatar) {
                Storage::disk('public')->delete($agent->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Update slug if name changed
        if ($agent->name !== $request->name) {
            $data['slug'] = $this->generateUniqueSlug($request->name, $agent->id);
        }

        $agent->update($data);

        return redirect()->route('agents.show', $agent)
            ->with('success', 'AI Agent updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AiAgent $agent)
    {
        // Check if user can delete this agent
        if ($agent->created_by !== auth()->id()) {
            abort(403);
        }

        // Delete avatar
        if ($agent->avatar) {
            Storage::disk('public')->delete($agent->avatar);
        }

        $agent->delete();

        return redirect()->route('agents.index')
            ->with('success', 'AI Agent deleted successfully!');
    }

    /**
     * Display public agents
     */
    public function public()
    {
        $agents = AiAgent::public()
            ->active()
            ->with('creator')
            ->latest()
            ->paginate(12);

        return view('agents.public', compact('agents'));
    }

    /**
     * Clone an agent
     */
    public function clone(AiAgent $agent)
    {
        if (!$agent->is_public && $agent->created_by !== auth()->id()) {
            abort(403);
        }

        $clonedAgent = $agent->replicate();
        $clonedAgent->name = $agent->name . ' (Copy)';
        $clonedAgent->slug = $this->generateUniqueSlug($clonedAgent->name);
        $clonedAgent->created_by = auth()->id();
        $clonedAgent->is_public = false;
        $clonedAgent->avatar = null; // Don't copy avatar
        $clonedAgent->save();

        return redirect()->route('agents.show', $clonedAgent)
            ->with('success', 'AI Agent cloned successfully!');
    }

    private function generateUniqueSlug(string $name, int $excludeId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (true) {
            $query = AiAgent::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            
            if (!$query->exists()) {
                break;
            }
            
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
