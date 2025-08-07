<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Installation') - AI Agents SaaS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-12 w-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-robot text-purple-600 text-2xl"></i>
                </div>
                <h1 class="mt-6 text-3xl font-extrabold text-white">
                    AI Agents SaaS Platform
                </h1>
                <p class="mt-2 text-sm text-purple-100">
                    Installation Wizard
                </p>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white/10 rounded-lg p-4">
                <div class="flex items-center justify-between text-white text-sm mb-2">
                    <span>Installation Progress</span>
                    <span id="progress-text">@yield('progress', '0')/6</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-2">
                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full transition-all duration-300" style="width: @yield('progress-width', '0%')"></div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There were some errors:
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>

            <!-- Footer -->
            <div class="text-center text-purple-100 text-sm">
                <p>&copy; {{ date('Y') }} AI Agents SaaS Platform. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')
</body>
</html>