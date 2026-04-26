<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CampusConnect') }} - Connect with Your Campus</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Additional Styles -->
        @stack('styles')

        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            .animate-float {
                animation: float 3s ease-in-out infinite;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-900 dark:to-gray-800 relative overflow-hidden">
            
            <!-- Background Decorative Elements -->
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-green-200 dark:bg-green-900/20 rounded-full blur-3xl opacity-30 animate-pulse"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-emerald-200 dark:bg-emerald-900/20 rounded-full blur-3xl opacity-30 animate-pulse" style="animation-delay: 2s;"></div>
            </div>

         

                <!-- App Name -->
                <h1 class="text-center text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ config('app.name', 'CampusConnect') }}
                </h1>
                <p class="text-center text-sm text-gray-600 dark:text-gray-400 mb-6">
                    Connect with events around your campus
                </p>

                <!-- Card Container with Glass Morphism Effect -->
                <div class="w-full sm:max-w-md px-6 py-8 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm shadow-2xl sm:rounded-2xl border border-gray-200 dark:border-gray-700">
                    {{ $slot }}
                </div>

                <!-- Footer with Links -->
                <div class="mt-8 text-center">
                    <div class="flex items-center justify-center space-x-4 text-sm">
                        <a href="#" class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition">
                            About
                        </a>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <a href="#" class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition">
                            Privacy
                        </a>
                        <span class="text-gray-300 dark:text-gray-600">•</span>
                        <a href="#" class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 transition">
                            Terms
                        </a>
                    </div>
                    <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">
                        &copy; {{ date('Y') }} {{ config('app.name', 'CampusConnect') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        @stack('scripts')
    </body>
</html>