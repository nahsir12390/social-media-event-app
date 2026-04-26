{{-- resources/views/dashboard.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}!</h3>
                            <p class="text-gray-600 mt-1">Here's what's happening with your events.</p>
                        </div>
                        <a href="{{ route('events.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create New Event
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                {{-- Events Created --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Events Created</p>
                                <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->events->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Events Attending --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-100 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Events Attending</p>
                                <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->attendees()->where('status', 'attending')->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Events Liked --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="p-3 bg-red-100 rounded-full">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Events Liked</p>
                                <p class="text-2xl font-bold text-gray-900">{{ auth()->user()->likes->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Your Upcoming Events --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Your Upcoming Events</h3>
                        
                        @php
                            $upcomingEvents = auth()->user()->events()
                                ->where('event_date', '>=', now())
                                ->orderBy('event_date', 'asc')
                                ->limit(3)
                                ->get();
                        @endphp

                        @if($upcomingEvents->count() > 0)
                            <div class="space-y-4">
                                @foreach($upcomingEvents as $event)
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $event->event_date->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <a href="{{ route('events.show', $event) }}" class="text-green-600 hover:text-green-800 text-sm">View</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">You haven't created any upcoming events.</p>
                        @endif
                    </div>
                </div>

                {{-- Events You're Attending --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Events You're Attending</h3>
                        
                        @php
                            $attendingEvents = auth()->user()->attendingEvents()
                                ->where('event_date', '>=', now())
                                ->orderBy('event_date', 'asc')
                                ->limit(3)
                                ->get();
                        @endphp

                        @if($attendingEvents->count() > 0)
                            <div class="space-y-4">
                                @foreach($attendingEvents as $event)
                                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $event->title }}</p>
                                            <p class="text-sm text-gray-500">{{ $event->event_date->format('M d, Y g:i A') }}</p>
                                        </div>
                                        <a href="{{ route('events.show', $event) }}" class="text-green-600 hover:text-green-800 text-sm">View</a>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">You're not attending any upcoming events.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Recent Events Feed --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Recent Events Near You</h3>
                        <a href="{{ route('events.feed') }}" class="text-green-600 hover:text-green-800 text-sm">View All →</a>
                    </div>

                    @php
                        $recentEvents = \App\Models\Event::with('user')
                            ->where('event_date', '>=', now())
                            ->where('is_active', true)
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($recentEvents as $event)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex items-start space-x-3">
                                    @if($event->user->profile_photo)
                                        <img src="{{ Storage::url($event->user->profile_photo) }}" 
                                             class="w-10 h-10 rounded-full object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($event->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $event->title }}</h4>
                                        <p class="text-sm text-gray-500">{{ $event->event_date->format('M d, g:i A') }}</p>
                                        <p class="text-xs text-gray-400 mt-1">by {{ $event->user->name }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>