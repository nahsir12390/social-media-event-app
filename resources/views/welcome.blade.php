<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<!-- Add Alpine.js for the mobile menu -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <title>CampusConnect - University Events & Social Hub</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.3; }
        }
        @keyframes slide-in {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fade-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }
        .animate-slide-in {
            animation: slide-in 1s ease-out forwards;
        }
        .animate-fade-up {
            animation: fade-up 0.8s ease-out forwards;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
   <!-- Navigation -->
<nav class="fixed top-0 w-full z-50 glass-effect dark:bg-gray-900/90" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <div class="w-10 h-10 bg-gradient-to-r from-green-600 to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent dark:from-green-400 dark:to-green-500">
                    CampusConnect
                </span>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="#features" class="text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 transition">Features</a>
                <a href="#how-it-works" class="text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 transition">How it Works</a>
                <a href="#testimonials" class="text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 transition">Testimonials</a>
                <a href="#stats" class="text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 transition">Stats</a>
            </div>

            <!-- Auth Buttons - Desktop -->
            <div class="hidden md:flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/events') }}" 
                           class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold text-sm hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-6 py-2.5 text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 font-semibold text-sm transition">
                            Log in
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" 
                               class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold text-sm hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-lg">
                                Sign Up
                            </a>
                        @endif
                    @endauth
                @endif
            </div>

            <!-- Mobile Menu Button and Auth -->
            <div class="flex items-center space-x-3 md:hidden">
                <!-- Mobile Auth Button (simplified) -->
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/events') }}" 
                           class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg text-xs font-semibold">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg text-xs font-semibold">
                            Login
                        </a>
                    @endauth
                @endif

                <!-- Hamburger Menu Button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition focus:outline-none">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div x-show="mobileMenuOpen" 
         @click.away="mobileMenuOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden glass-effect dark:bg-gray-900/90 border-t border-gray-200 dark:border-gray-800 shadow-lg">
        <div class="px-4 py-3 space-y-2">
            <a href="#features" 
               @click="mobileMenuOpen = false"
               class="block py-3 px-4 text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition">
                Features
            </a>
            <a href="#how-it-works" 
               @click="mobileMenuOpen = false"
               class="block py-3 px-4 text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition">
                How it Works
            </a>
            <a href="#testimonials" 
               @click="mobileMenuOpen = false"
               class="block py-3 px-4 text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition">
                Testimonials
            </a>
            <a href="#stats" 
               @click="mobileMenuOpen = false"
               class="block py-3 px-4 text-gray-700 dark:text-gray-300 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition">
                Stats
            </a>
            
            <!-- Mobile Auth Links (for when user is logged out) -->
            @if (Route::has('login') && !auth()->check())
                <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" 
                           @click="mobileMenuOpen = false"
                           class="block py-3 px-4 text-green-600 dark:text-green-400 font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition">
                            Create Account
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</nav>

    <!-- Hero Section -->
    <section class="relative pt-24 pb-20 overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 -z-10">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-green-100 dark:bg-green-900/20 rounded-full blur-3xl opacity-30 animate-pulse-slow"></div>
            <div class="absolute bottom-0 right-0 w-[600px] h-[600px] bg-green-100 dark:bg-green-900/20 rounded-full blur-3xl opacity-30 animate-pulse-slow" style="animation-delay: 2s;"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left animate-fade-up">
                    <div class="inline-flex items-center px-4 py-2 bg-green-100 dark:bg-green-900/30 rounded-full mb-6">
                        <span class="w-2 h-2 bg-green-600 rounded-full animate-pulse mr-2"></span>
                        <span class="text-sm font-semibold text-green-700 dark:text-green-300">📍 Location-Based Social Hub</span>
                    </div>
                    
                    <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6">
                        <span class="bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent">
                            Discover
                        </span>
                        <br />
                        <span class="text-gray-900 dark:text-white">
                            Events Around You
                        </span>
                    </h1>
                    
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto lg:mx-0">
                        Connect with your campus community, discover exciting events, and never miss out on what's happening near you. Your campus social life, reimagined.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start mb-12">
                        @auth
                            <a href="{{ route('events.feed') }}" 
                               class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-xl flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                </svg>
                                Go to Events Feed
                            </a>
                        @else
                            <a href="{{ route('register') }}" 
                               class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-xl flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                                Get Started Free
                            </a>
                        @endauth
                        
                        <a href="#features" 
                           class="px-8 py-4 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-700 transition-all transform hover:scale-105 shadow-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            See How It Works
                        </a>
                    </div>

                    <!-- Social Proof -->
                    <div class="flex flex-col sm:flex-row items-center gap-6">
                        <div class="flex -space-x-3">
                            @foreach(['JD', 'MK', 'AL', 'SR'] as $initial)
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-600 to-green-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-white text-sm font-bold shadow-lg">
                                    {{ $initial }}
                                </div>
                            @endforeach
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 border-2 border-white dark:border-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 text-sm font-bold shadow-lg">
                                +2k
                            </div>
                        </div>
                        <p class="text-gray-600 dark:text-gray-300">
                            <span class="font-bold text-gray-900 dark:text-white">2,000+</span> students already joined
                        </p>
                    </div>
                </div>

                <!-- Right Content - Dashboard Preview -->
                <div class="relative lg:block animate-float">
                    <div class="relative">
                        <!-- Main Dashboard Card -->
                        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            <div class="p-4 bg-gradient-to-r from-green-600 to-green-700">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 bg-red-400 rounded-full"></div>
                                        <div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
                                        <div class="w-3 h-3 bg-green-400 rounded-full"></div>
                                    </div>
                                    <span class="text-white text-sm font-medium">Events Feed</span>
                                    <div class="w-6"></div>
                                </div>
                            </div>
                            
                            <div class="p-6">
                                <!-- Event Cards -->
                                <div class="space-y-4">
                                    @foreach([
                                        ['title' => 'Tech Hackathon', 'time' => 'Today, 2:00 PM', 'attendees' => 45, 'location' => 'Engineering Building'],
                                        ['title' => 'Career Fair 2026', 'time' => 'Tomorrow, 10:00 AM', 'attendees' => 120, 'location' => 'Student Center'],
                                        ['title' => 'Music Festival', 'time' => 'Fri, 6:00 PM', 'attendees' => 300, 'location' => 'Campus Quad']
                                    ] as $event)
                                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                                                {{ substr($event['title'], 0, 1) }}
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $event['title'] }}</h4>
                                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>{{ $event['time'] }}</span>
                                                    <span class="mx-2">•</span>
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                    <span>{{ $event['attendees'] }} attending</span>
                                                </div>
                                            </div>
                                            <button class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg text-xs font-semibold">
                                                Join
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Mini Map Preview -->
                                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Nearby Events</span>
                                        <span class="text-xs text-green-600">View Map →</span>
                                    </div>
                                    <div class="h-24 bg-gradient-to-r from-green-100 to-green-100 dark:from-green-900/30 dark:to-green-900/30 rounded-lg relative overflow-hidden">
                                        <!-- Simulated map markers -->
                                        <div class="absolute top-4 left-4 w-2 h-2 bg-green-600 rounded-full animate-ping"></div>
                                        <div class="absolute top-8 left-12 w-2 h-2 bg-green-600 rounded-full animate-ping" style="animation-delay: 0.5s;"></div>
                                        <div class="absolute bottom-4 right-8 w-2 h-2 bg-pink-600 rounded-full animate-ping" style="animation-delay: 1s;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Stats Card -->
                        <div class="absolute -bottom-6 -left-6 bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 border border-gray-200 dark:border-gray-700 hidden lg:block">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">15+</p>
                                    <p class="text-xs text-gray-500">Events Today</p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating User Card -->
                        <div class="absolute -top-6 -right-6 bg-white dark:bg-gray-800 rounded-xl shadow-xl p-4 border border-gray-200 dark:border-gray-700 hidden lg:block">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">500+</p>
                                    <p class="text-xs text-gray-500">Active Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Everything You Need in One Place
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Discover, connect, and engage with your campus community like never before
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>',
                        'title' => 'Location-Based',
                        'desc' => 'Find events happening near you, on campus and beyond'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>',
                        'title' => 'Connect with Peers',
                        'desc' => 'Meet like-minded students who share your interests'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>',
                        'title' => 'Create Events',
                        'desc' => 'Easily create and manage your own campus events'
                    ],
                    [
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
                        'title' => 'Live Updates',
                        'desc' => 'Get real-time notifications about events you care about'
                    ]
                ] as $feature)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg card-hover border border-gray-200 dark:border-gray-700">
                        <div class="w-14 h-14 bg-gradient-to-r from-green-600 to-green-700 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                {!! $feature['icon'] !!}
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ $feature['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    How CampusConnect Works
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Three simple steps to transform your campus social life
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Connection Lines (visible on desktop) -->
                <div class="hidden lg:block absolute top-1/3 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-green-300 to-green-300 -translate-y-1/2 z-0"></div>

                @foreach([
                    [
                        'step' => '1',
                        'title' => 'Create Profile',
                        'desc' => 'Sign up with your university email and set up your profile',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>'
                    ],
                    [
                        'step' => '2',
                        'title' => 'Discover Events',
                        'desc' => 'Browse events near you based on your interests and location',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>'
                    ],
                    [
                        'step' => '3',
                        'title' => 'Connect & Engage',
                        'desc' => 'Join events, meet new people, and make memories',
                        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    ]
                ] as $item)
                    <div class="relative z-10 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-600 to-green-700 rounded-2xl text-white text-2xl font-bold mb-6 shadow-xl">
                            {{ $item['step'] }}
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 dark:text-gray-300">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section id="stats" class="py-24 bg-green-600">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 text-center">
                @foreach([
                    ['value' => '2,500+', 'label' => 'Active Students'],
                    ['value' => '150+', 'label' => 'Events Monthly'],
                    ['value' => '30+', 'label' => 'Partner Clubs'],
                    ['value' => '98%', 'label' => 'Satisfaction Rate']
                ] as $stat)
                    <div class="bg-white/10 backdrop-blur rounded-2xl p-8 border border-white/20">
                        <div class="text-4xl font-bold text-white mb-2">{{ $stat['value'] }}</div>
                        <div class="text-white/80">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-24 bg-gray-50 dark:bg-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    What Students Say
                </h2>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                    Join thousands of satisfied students who've transformed their campus experience
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @foreach([
                    [
                        'name' => 'Sarah Johnson',
                        'role' => 'Computer Science',
                        'quote' => 'CampusConnect helped me find hackathons and tech meetups I never knew existed. Made amazing connections!',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Mike Chen',
                        'role' => 'Business Admin',
                        'quote' => 'The networking opportunities are incredible. I found my internship through an event on this platform.',
                        'rating' => 5
                    ],
                    [
                        'name' => 'Emma Davis',
                        'role' => 'Visual Arts',
                        'quote' => 'As an international student, this app helped me feel connected to campus life immediately.',
                        'rating' => 5
                    ]
                ] as $testimonial)
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg card-hover border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-600 to-green-700 flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($testimonial['name'], 0, 1) }}
                            </div>
                            <div class="ml-4">
                                <h4 class="font-bold text-gray-900 dark:text-white">{{ $testimonial['name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $testimonial['role'] }}</p>
                            </div>
                        </div>
                        <div class="flex mb-3">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                        </div>
                        <p class="text-gray-600 dark:text-gray-300 italic">"{{ $testimonial['quote'] }}"</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-gray-900 dark:text-white mb-6">
                Ready to Transform Your Campus Experience?
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-10">
                Join CampusConnect today and never miss out on what's happening around you.
            </p>
            @auth
                <a href="{{ route('events.feed') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-xl">
                    Go to Events Feed
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-semibold hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105 shadow-xl">
                    Get Started Free
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @endauth
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold">CampusConnect</span>
                    </div>
                    <p class="text-gray-400 text-sm">Your gateway to campus events and connections.</p>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#how-it-works" class="hover:text-white transition">How it Works</a></li>
                        <li><a href="#pricing" class="hover:text-white transition">Pricing</a></li>
                        <li><a href="#faq" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#about" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#careers" class="hover:text-white transition">Careers</a></li>
                        <li><a href="#blog" class="hover:text-white transition">Blog</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#privacy" class="hover:text-white transition">Privacy Policy</a></li>
                        <li><a href="#terms" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#cookies" class="hover:text-white transition">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} CampusConnect. All rights reserved. Made with ❤️ for university students.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });

                // Close mobile menu when clicking a link
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', function() {
                        mobileMenu.classList.add('hidden');
                    });
                });
            }

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>