@extends('layouts.app')

@section('title', 'Create AI Agent')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('agents.index') }}" 
               class="inline-flex items-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 mr-4">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Agents
            </a>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Create AI Agent</h1>
        <p class="text-gray-600 dark:text-gray-400">
            Create a custom AI agent with unique personality, knowledge, and capabilities.
        </p>
    </div>

    <form action="{{ route('agents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Agent Name -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Agent Name *
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="e.g., Marketing Assistant, Code Helper, Creative Writer"
                           required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category
                    </label>
                    <select id="category" 
                            name="category" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select a category</option>
                        <option value="Business" {{ old('category') == 'Business' ? 'selected' : '' }}>Business</option>
                        <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                        <option value="Creative" {{ old('category') == 'Creative' ? 'selected' : '' }}>Creative</option>
                        <option value="Technical" {{ old('category') == 'Technical' ? 'selected' : '' }}>Technical</option>
                        <option value="Health" {{ old('category') == 'Health' ? 'selected' : '' }}>Health</option>
                        <option value="Finance" {{ old('category') == 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option value="Entertainment" {{ old('category') == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Output Language -->
                <div>
                    <label for="output_language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Output Language
                    </label>
                    <select id="output_language" 
                            name="output_language" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="English" {{ old('output_language', 'English') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="Spanish" {{ old('output_language') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="French" {{ old('output_language') == 'French' ? 'selected' : '' }}>French</option>
                        <option value="German" {{ old('output_language') == 'German' ? 'selected' : '' }}>German</option>
                        <option value="Italian" {{ old('output_language') == 'Italian' ? 'selected' : '' }}>Italian</option>
                        <option value="Portuguese" {{ old('output_language') == 'Portuguese' ? 'selected' : '' }}>Portuguese</option>
                        <option value="Chinese" {{ old('output_language') == 'Chinese' ? 'selected' : '' }}>Chinese</option>
                        <option value="Japanese" {{ old('output_language') == 'Japanese' ? 'selected' : '' }}>Japanese</option>
                        <option value="Korean" {{ old('output_language') == 'Korean' ? 'selected' : '' }}>Korean</option>
                        <option value="Arabic" {{ old('output_language') == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                        <option value="Russian" {{ old('output_language') == 'Russian' ? 'selected' : '' }}>Russian</option>
                    </select>
                    @error('output_language')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Describe what this agent does and how it can help users...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Avatar Upload -->
                <div class="md:col-span-2">
                    <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Avatar Image
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-robot text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   id="avatar" 
                                   name="avatar" 
                                   accept="image/*"
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Upload a square image (recommended: 256x256px, max 2MB)
                            </p>
                        </div>
                    </div>
                    @error('avatar')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- AI Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">AI Configuration</h3>
            
            <div class="space-y-6">
                <!-- AI Model -->
                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        AI Model *
                    </label>
                    <select id="model" 
                            name="model" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                        @foreach($availableModels as $modelKey => $modelName)
                            <option value="{{ $modelKey }}" {{ old('model', 'gpt-3.5-turbo') == $modelKey ? 'selected' : '' }}>
                                {{ $modelName }}
                            </option>
                        @endforeach
                    </select>
                    @error('model')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- System Prompt -->
                <div>
                    <label for="system_prompt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        System Prompt *
                    </label>
                    <textarea id="system_prompt" 
                              name="system_prompt" 
                              rows="6" 
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="You are a helpful assistant that..."
                              required>{{ old('system_prompt') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Define the agent's personality, knowledge, and behavior. This is the core instruction that shapes how the AI responds.
                    </p>
                    @error('system_prompt')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Welcome Message -->
                <div>
                    <label for="welcome_message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Welcome Message
                    </label>
                    <textarea id="welcome_message" 
                              name="welcome_message" 
                              rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Hi! I'm here to help you with...">{{ old('welcome_message') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        The first message users see when they start chatting with this agent.
                    </p>
                    @error('welcome_message')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Advanced Parameters -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Temperature -->
                    <div>
                        <label for="temperature" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Temperature
                        </label>
                        <input type="number" 
                               id="temperature" 
                               name="temperature" 
                               value="{{ old('temperature', '0.7') }}" 
                               min="0" 
                               max="2" 
                               step="0.1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Controls creativity (0 = focused, 2 = very creative)
                        </p>
                        @error('temperature')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Tokens -->
                    <div>
                        <label for="max_tokens" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Max Tokens
                        </label>
                        <input type="number" 
                               id="max_tokens" 
                               name="max_tokens" 
                               value="{{ old('max_tokens', '1000') }}" 
                               min="100" 
                               max="4000"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Maximum length of responses
                        </p>
                        @error('max_tokens')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Top P -->
                    <div>
                        <label for="top_p" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Top P
                        </label>
                        <input type="number" 
                               id="top_p" 
                               name="top_p" 
                               value="{{ old('top_p', '1.0') }}" 
                               min="0" 
                               max="1" 
                               step="0.1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Controls diversity of word choice
                        </p>
                        @error('top_p')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Frequency Penalty -->
                    <div>
                        <label for="frequency_penalty" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Frequency Penalty
                        </label>
                        <input type="number" 
                               id="frequency_penalty" 
                               name="frequency_penalty" 
                               value="{{ old('frequency_penalty', '0.0') }}" 
                               min="-2" 
                               max="2" 
                               step="0.1"
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Reduces repetition (-2 to 2)
                        </p>
                        @error('frequency_penalty')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Style & Behavior -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Style & Behavior</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tone -->
                <div>
                    <label for="tone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Tone
                    </label>
                    <select id="tone" 
                            name="tone" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select tone</option>
                        <option value="Professional" {{ old('tone') == 'Professional' ? 'selected' : '' }}>Professional</option>
                        <option value="Friendly" {{ old('tone') == 'Friendly' ? 'selected' : '' }}>Friendly</option>
                        <option value="Casual" {{ old('tone') == 'Casual' ? 'selected' : '' }}>Casual</option>
                        <option value="Formal" {{ old('tone') == 'Formal' ? 'selected' : '' }}>Formal</option>
                        <option value="Enthusiastic" {{ old('tone') == 'Enthusiastic' ? 'selected' : '' }}>Enthusiastic</option>
                        <option value="Empathetic" {{ old('tone') == 'Empathetic' ? 'selected' : '' }}>Empathetic</option>
                        <option value="Witty" {{ old('tone') == 'Witty' ? 'selected' : '' }}>Witty</option>
                    </select>
                    @error('tone')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Writing Style -->
                <div>
                    <label for="writing_style" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Writing Style
                    </label>
                    <select id="writing_style" 
                            name="writing_style" 
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">Select style</option>
                        <option value="Concise" {{ old('writing_style') == 'Concise' ? 'selected' : '' }}>Concise</option>
                        <option value="Detailed" {{ old('writing_style') == 'Detailed' ? 'selected' : '' }}>Detailed</option>
                        <option value="Conversational" {{ old('writing_style') == 'Conversational' ? 'selected' : '' }}>Conversational</option>
                        <option value="Academic" {{ old('writing_style') == 'Academic' ? 'selected' : '' }}>Academic</option>
                        <option value="Creative" {{ old('writing_style') == 'Creative' ? 'selected' : '' }}>Creative</option>
                        <option value="Technical" {{ old('writing_style') == 'Technical' ? 'selected' : '' }}>Technical</option>
                        <option value="Storytelling" {{ old('writing_style') == 'Storytelling' ? 'selected' : '' }}>Storytelling</option>
                    </select>
                    @error('writing_style')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Visibility Settings -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Visibility Settings</h3>
            
            <div class="space-y-4">
                <!-- Public Access -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_public" 
                           name="is_public" 
                           value="1"
                           {{ old('is_public') ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500 focus:ring-2">
                    <label for="is_public" class="ml-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Make this agent public</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Allow other users to discover and chat with this agent
                        </p>
                    </label>
                </div>

                <!-- Active Status -->
                <div class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="w-4 h-4 text-purple-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500 focus:ring-2">
                    <label for="is_active" class="ml-3">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</span>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Enable this agent for chatting
                        </p>
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('agents.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Cancel
            </a>
            
            <button type="submit" 
                    class="inline-flex items-center px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium">
                <i class="fas fa-save mr-2"></i>
                Create Agent
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Preview avatar upload
document.getElementById('avatar').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // You could add image preview functionality here
        };
        reader.readAsDataURL(file);
    }
});

// Auto-generate slug from name (optional enhancement)
document.getElementById('name').addEventListener('input', function(e) {
    // Could add real-time slug preview here
});
</script>
@endpush
@endsection