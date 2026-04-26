<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events Map</h1>
                <p class="mt-1 text-sm text-gray-500">Discover events happening around you</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                {{-- Radius selector with modern styling --}}
                <div class="relative">
                    <select wire:model.live="radius" 
                            class="appearance-none bg-white pl-4 pr-10 py-3 rounded-xl border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 w-full sm:w-48">
                        <option value="1">📍 1km radius</option>
                        <option value="5">📍 5km radius</option>
                        <option value="10">📍 10km radius</option>
                        <option value="25">📍 25km radius</option>
                        <option value="50">📍 50km radius</option>
                    </select>
                    <svg class="w-5 h-5 text-gray-400 absolute right-3 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>

                {{-- Location button --}}
                <button wire:click="$dispatch('request-location')" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all transform hover:scale-105 shadow-lg w-full sm:w-auto">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Use My Location
                </button>
            </div>
        </div>

        {{-- Map Container --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Map --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
                <div id="map" class="h-[600px] w-full"></div>
            </div>

            {{-- Sidebar with event list or selected event details --}}
            <div class="lg:col-span-1">
                @if($selectedEvent)
                    {{-- Selected Event Details --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Event Details
                            </h3>
                            <button wire:click="selectEvent(null)" 
                                    class="p-1 hover:bg-gray-100 rounded-lg transition">
                                <svg class="w-5 h-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        @if($selectedEvent->image)
                            <img src="{{ Storage::url($selectedEvent->image) }}" 
                                 alt="{{ $selectedEvent->title }}"
                                 class="w-full h-40 object-cover rounded-xl mb-4">
                        @else
                            <div class="w-full h-40 bg-gradient-to-r from-green-600 to-green-700 rounded-xl mb-4 flex items-center justify-center">
                                <span class="text-white text-3xl font-bold">{{ substr($selectedEvent->title, 0, 1) }}</span>
                            </div>
                        @endif

                        <h4 class="font-bold text-xl text-gray-900 mb-2">{{ $selectedEvent->title }}</h4>
                        <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $selectedEvent->description }}</p>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $selectedEvent->event_date->format('M d, Y g:i A') }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="line-clamp-1">{{ $selectedEvent->location_name }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Hosted by {{ $selectedEvent->user->name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex space-x-3">
                                <span class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"></path>
                                    </svg>
                                    {{ $selectedEvent->likes->count() }}
                                </span>
                                <span class="flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-1 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 9.933a4 4 0 00-.8 2.4z"></path>
                                    </svg>
                                    {{ $selectedEvent->attendees->where('status', 'attending')->count() }}
                                </span>
                            </div>
                            <a href="{{ route('events.show', $selectedEvent) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition text-sm font-medium">
                                View Full Details
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Events List --}}
                    <div class="bg-white rounded-2xl shadow-xl p-6 border border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Events on Map ({{ $events->count() }})
                        </h3>
                        
                        <div class="space-y-3 max-h-[500px] overflow-y-auto custom-scrollbar pr-2">
                            @forelse($events as $event)
                                <button wire:click="selectEvent({{ $event->id }})" 
                                        class="w-full text-left p-4 rounded-xl hover:bg-gray-50 transition-all {{ $selectedEvent && $selectedEvent->id === $event->id ? 'bg-green-50 ring-2 ring-green-200' : '' }}">
                                    <div class="flex items-start space-x-3">
                                        @if($event->image)
                                            <img src="{{ Storage::url($event->image) }}" 
                                                 class="w-16 h-16 object-cover rounded-xl">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-xl font-bold">{{ substr($event->title, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-gray-900 truncate">{{ $event->title }}</p>
                                            <p class="text-xs text-gray-500 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $event->event_date->format('M d, g:i A') }}
                                            </p>
                                            @if(property_exists($event, 'distance'))
                                                <p class="text-xs text-green-600 mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ round($event->distance, 1) }} km away
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </button>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <p class="text-gray-500 text-sm mb-2">No events found in this area</p>
                                    <a href="{{ route('events.create') }}" 
                                       class="inline-flex items-center text-green-600 hover:text-green-800 text-sm font-medium">
                                        Create an event
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Map Script --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            let map;
            let markers = [];
            let userMarker = null;
            let mapInitialized = false;

            function initMap() {
                if (mapInitialized) return;
                
                const mapContainer = document.getElementById('map');
                if (!mapContainer) return;
                
                if (mapContainer.clientHeight === 0 || mapContainer.clientWidth === 0) {
                    setTimeout(initMap, 100);
                    return;
                }
                
                const userLat = {{ $userLatitude ?? 8.8481 }};
                const userLng = {{ $userLongitude ?? 7.8736 }};
                
                if (window.mapInstance) {
                    window.mapInstance.remove();
                }
                
                map = L.map('map', {
                    center: [userLat, userLng],
                    zoom: 12,
                    zoomControl: true,
                    fadeAnimation: true,
                    markerZoomAnimation: true
                });
                
                window.mapInstance = map;
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Add scale control
                L.control.scale({ imperial: false, metric: true }).addTo(map);

                // Custom user location icon
                const userIcon = L.divIcon({
                    className: 'user-location-marker',
                    html: `<div class="relative">
                            <div class="w-4 h-4 bg-blue-500 rounded-full border-2 border-white shadow-lg"></div>
                            <div class="absolute inset-0 animate-ping bg-blue-400 rounded-full opacity-75"></div>
                        </div>`,
                    iconSize: [16, 16]
                });

                userMarker = L.marker([userLat, userLng], { icon: userIcon }).addTo(map);
                userMarker.bindPopup(`
                    <div class="text-center">
                        <strong>Your Location</strong>
                    </div>
                `);

                const events = @json($events);
                addEventMarkers(events);
                
                mapInitialized = true;
            }

            function addEventMarkers(events) {
                if (!map) return;
                
                if (markers.length > 0) {
                    markers.forEach(marker => map.removeLayer(marker));
                    markers = [];
                }

                events.forEach(event => {
                    if (!event.latitude || !event.longitude) return;
                    
                    const eventIcon = L.divIcon({
                        className: 'event-marker',
                        html: `<div class="relative group">
                                <div class="w-8 h-8 bg-gradient-to-r from-green-600 to-green-700 rounded-full border-2 border-white shadow-lg flex items-center justify-center text-white text-sm font-bold transform group-hover:scale-110 transition">
                                    ${event.title.charAt(0)}
                                </div>
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 hidden group-hover:block">
                                    <div class="bg-gray-900 text-white text-xs rounded py-1 px-2 whitespace-nowrap">
                                        ${event.title}
                                    </div>
                                </div>
                            </div>`,
                        iconSize: [32, 32],
                        iconAnchor: [16, 16],
                        popupAnchor: [0, -16]
                    });

                    const marker = L.marker([event.latitude, event.longitude], { 
                        icon: eventIcon,
                        riseOnHover: true
                    }).addTo(map);

                    marker.on('click', () => {
                        Livewire.dispatch('selectEvent', { eventId: event.id });
                    });

                    marker.bindPopup(`
                        <div style="min-width: 200px;">
                            <strong style="font-size: 14px;">${event.title}</strong><br>
                            <span style="font-size: 12px;">${new Date(event.event_date).toLocaleString()}</span><br>
                            <a href="/events/${event.id}" style="color: #4f46e5; font-size: 12px; margin-top: 8px; display: inline-block;">View Details →</a>
                        </div>
                    `);

                    markers.push(marker);
                });
            }

            if (document.getElementById('map')) {
                setTimeout(initMap, 100);
            }

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
                            console.error('Geolocation error:', error);
                            // Use default location (Keffi, Nigeria) if geolocation fails
                            const defaultLat = 8.8481;
                            const defaultLng = 7.8736;
                            Livewire.dispatch('setLocation', {
                                latitude: defaultLat,
                                longitude: defaultLng
                            });
                            alert('Geolocation unavailable on this connection. Using default location.\n\nNote: Location services work on localhost or HTTPS. Access http://localhost:8000 for full functionality.');
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

            Livewire.on('setLocation', (data) => {
                if (userMarker && map) {
                    userMarker.setLatLng([data.latitude, data.longitude]);
                    map.setView([data.latitude, data.longitude], 12);
                    
                    userMarker.bindPopup(`
                        <div class="text-center">
                            <strong>Your Location</strong>
                        </div>
                    `).openPopup();
                }
            });

            Livewire.on('event-selected', (data) => {
                if (map) {
                    map.setView([data.lat, data.lng], 15, {
                        animate: true,
                        duration: 0.5
                    });
                }
            });

            Livewire.hook('morph.updated', (component) => {
                if (component.name === 'map-view') {
                    setTimeout(() => {
                        if (map && document.getElementById('map')) {
                            const events = @json($events);
                            addEventMarkers(events);
                        } else {
                            mapInitialized = false;
                            initMap();
                        }
                    }, 100);
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        .user-location-marker {
            background: transparent;
            border: none;
        }
        .event-marker {
            background: transparent;
            border: none;
            cursor: pointer;
        }
        #map {
            min-height: 600px;
            z-index: 1;
            border-radius: 1rem;
        }
        .leaflet-container {
            border-radius: 1rem;
            font-family: inherit;
        }
        .leaflet-popup-content {
            font-size: 13px;
            line-height: 1.4;
            margin: 10px 12px;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        @keyframes ping {
            75%, 100% {
                transform: scale(2);
                opacity: 0;
            }
        }
        .animate-ping {
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
    </style>
    @endpush
</div>