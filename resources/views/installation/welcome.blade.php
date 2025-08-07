@extends('installation.layout')

@section('title', 'Welcome')
@section('progress', '1')
@section('progress-width', '16.6%')

@section('content')
<div class="text-center">
    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gradient-to-r from-purple-500 to-blue-600 mb-6">
        <i class="fas fa-rocket text-white text-2xl"></i>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-900 mb-4">
        Welcome to AI Agents SaaS Platform
    </h2>
    
    <p class="text-gray-600 mb-8 leading-relaxed">
        Thank you for choosing our AI Agents SaaS platform! This installation wizard will guide you through the setup process in just a few simple steps.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-robot text-purple-500 mr-2"></i>
                <h3 class="font-semibold text-gray-900">AI Chatbots</h3>
            </div>
            <p class="text-sm text-gray-600">Create and manage multiple AI agents with custom personalities and training.</p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-comments text-blue-500 mr-2"></i>
                <h3 class="font-semibold text-gray-900">Chat Interface</h3>
            </div>
            <p class="text-sm text-gray-600">ChatGPT-like interface with real-time messaging and conversation history.</p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-coins text-yellow-500 mr-2"></i>
                <h3 class="font-semibold text-gray-900">Credit System</h3>
            </div>
            <p class="text-sm text-gray-600">Flexible credit-based pricing with multiple payment options.</p>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-tachometer-alt text-green-500 mr-2"></i>
                <h3 class="font-semibold text-gray-900">Admin Dashboard</h3>
            </div>
            <p class="text-sm text-gray-600">Comprehensive admin panel for managing users, agents, and analytics.</p>
        </div>
    </div>
    
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3 text-left">
                <h3 class="text-sm font-medium text-blue-800">Before You Begin</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Make sure you have:</p>
                    <ul class="list-disc pl-5 mt-1">
                        <li>MySQL database credentials</li>
                        <li>SMTP email settings (optional)</li>
                        <li>OpenAI API key (for AI functionality)</li>
                        <li>Stripe/PayPal credentials (for payments)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('installation.requirements') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
        <i class="fas fa-arrow-right mr-2"></i>
        Start Installation
    </a>
</div>
@endsection