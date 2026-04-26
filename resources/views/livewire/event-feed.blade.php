<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header with Create Button --}}
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events Feed</h1>
                <p class="mt-1 text-sm text-gray-500">Discover and join amazing events near you</p>
            </div>
            <a href="{{ route('events.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-105 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Event
            </a>
        </div>

        {{-- Search and Filters --}}
        <div class="bg-white rounded-2xl shadow-xl mb-8 p-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Events</label>
                    <div class="relative">
                        <input type="text" 
                               wire:model.live.debounce.300ms="search" 
                               placeholder="Search by title, description, or location..."
                               class="w-full pl-10 pr-4 py-3 rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 transition">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select wire:model.live="filter" 
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3">
                        <option value="latest">✨ Latest Events</option>
                        <option value="nearby">📍 Nearby ({{ $radius }}km)</option>
                        <option value="upcoming">📅 Upcoming</option>
                        <option value="popular">🔥 Most Popular</option>
                    </select>
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">When</label>
                    <select wire:model.live="dateRange" 
                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-3">
                        <option value="all">🗓️ All Dates</option>
                        <option value="today">🕐 Today</option>
                        <option value="this_week">📆 This Week</option>
                        <option value="this_month">📅 This Month</option>
                    </select>
                </div>
            </div>

            {{-- Location Status Bar --}}
            <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 {{ $userLatitude && $userLongitude ? 'text-green-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="ml-2 text-sm {{ $userLatitude && $userLongitude ? 'text-gray-700' : 'text-gray-500' }}">
                            @if($userLatitude && $userLongitude)
                                Location set
                            @else
                                Location not set
                            @endif
                        </span>
                    </div>
                    
                    @if($userLatitude && $userLongitude)
                        <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">
                            Showing events within {{ $radius }}km
                        </span>
                    @endif
                </div>

                <div class="flex space-x-2">
                    @if($userLatitude && $userLongitude)
                        <button wire:click="$dispatch('request-location')" 
                                class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Update Location
                        </button>
                        <button wire:click="clearLocation" 
                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm">
                            Clear
                        </button>
                    @else
                        <button wire:click="$dispatch('request-location')" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Enable Location for Nearby Events
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Events Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <div class="group bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                    {{-- Event Image with Overlay --}}
                    <div class="relative h-52 overflow-hidden">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" 
                                 alt="{{ $event->title }}"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-green-600 to-green-700 flex items-center justify-center">
                                <span class="text-white text-5xl font-bold">{{ substr($event->title, 0, 1) }}</span>
                            </div>
                        @endif
                        
                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                        
                        {{-- Date Badge --}}
                        <div class="absolute top-4 right-4 bg-white/95 backdrop-blur rounded-xl shadow-lg px-4 py-2 text-center">
                            <span class="block text-xs font-bold text-green-600 uppercase tracking-wider">
                                {{ $event->event_date->format('M') }}
                            </span>
                            <span class="block text-2xl font-bold text-gray-900 leading-none">
                                {{ $event->event_date->format('d') }}
                            </span>
                        </div>

                        {{-- Distance Badge (if nearby) --}}
                        @if(property_exists($event, 'distance'))
                            <div class="absolute bottom-4 left-4 bg-black/50 backdrop-blur text-white px-3 py-1.5 rounded-full text-xs font-medium">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ round($event->distance, 1) }} km away
                            </div>
                        @endif
                    </div>

                    {{-- Event Details --}}
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="text-xl font-bold text-gray-900 line-clamp-1 group-hover:text-green-600 transition">
                                {{ $event->title }}
                            </h3>
                        </div>

                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {{ $event->description }}
                        </p>

                        {{-- Event Meta with Icons --}}
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $event->event_date->format('l, F j, Y • g:i A') }}</span>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="line-clamp-1">{{ $event->location_name }}</span>
                            </div>
                            
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Hosted by {{ $event->user->name }}</span>
                            </div>
                        </div>

                        {{-- Interaction Stats with Modern Badges --}}
                        <div class="flex items-center justify-between border-t border-gray-100 pt-4">
                            <div class="flex space-x-4">
                                <span class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-1 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                                    </svg>
                                    {{ $event->likes->count() }}
                                </span>
                                <span class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 9.933a4 4 0 00-.8 2.4z"></path>
                                    </svg>
                                    {{ $event->attendees->where('status', 'attending')->count() }}
                                </span>
                                <span class="flex items-center text-sm text-gray-500">
                                    <svg class="w-5 h-5 mr-1 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"></path>
                                        <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"></path>
                                    </svg>
                                    {{ $event->comments->count() }}
                                </span>
                            </div>
                            
                            <a href="{{ route('events.show', $event) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm font-medium">
                                View Details
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-2xl shadow-xl p-16 text-center border border-gray-100">
                        <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No events found</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">
                            @if($search || $filter !== 'latest')
                                Try adjusting your search or filters to find more events.
                            @else
                                Be the first to create an event and get the party started!
                            @endif
                        </p>
                        <a href="{{ route('events.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Create Your First Event
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $events->links() }}
        </div>
    </div>

    {{-- Location Request Script --}}
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('request-location', () => {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            Livewire.dispatch('setLocation', {
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude
                            });
                        },
                        (error) => {
                            console.error('Error getting location:', error);
                            alert('Unable to get your location. Please enable location services.');
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 5000,
                            maximumAge: 0
                        }
                    );
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            });

            Livewire.on('notify', (data) => {
                // You can integrate a toast notification library here
                console.log('Notification:', data.message, data.type);
            });
        });
    </script>
</div>