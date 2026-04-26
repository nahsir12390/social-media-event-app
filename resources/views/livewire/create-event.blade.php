<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Event</h2>

                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                <form wire:submit="save" class="space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Event Title</label>
                        <input type="text" 
                               wire:model="title" 
                               id="title"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                               placeholder="Enter event title">
                        @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" 
                                  id="description" 
                                  rows="5"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                  placeholder="Describe your event..."></textarea>
                        @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Event Date --}}
                    <div>
                        <label for="event_date" class="block text-sm font-medium text-gray-700">Event Date & Time</label>
                        <input type="datetime-local" 
                               wire:model="event_date" 
                               id="event_date"
                               min="{{ now()->format('Y-m-d\TH:i') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('event_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Location Search --}}
                    <div class="relative" x-data="{ open: @entangle('showResults') }">
                        <label for="searchLocation" class="block text-sm font-medium text-gray-700">Search Location</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="text" 
                                   wire:model.live.debounce.500ms="searchLocation" 
                                   id="searchLocation"
                                   autocomplete="off"
                                   placeholder="Search for a location in Nigeria (e.g., Nsuk University Keffi)"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 pr-10
                                          @if(strlen($searchLocation) > 2 && !$isSearching && $showResults && count($searchResults) === 0) border-yellow-300 bg-yellow-50 @endif
                                          @if($latitude && $longitude) border-green-300 bg-green-50 @endif">
                            
                            {{-- Status Icons --}}
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                @if($isSearching)
                                    <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                @elseif($latitude && $longitude && $searchLocation)
                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif(strlen($searchLocation) > 2 && !$isSearching && $showResults && count($searchResults) === 0)
                                    <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        
                        <p class="mt-1 text-xs text-gray-500 flex items-center">
                            <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Search for a location, then click on the map to fine-tune the exact spot
                        </p>
                        
                        {{-- Search Results Dropdown --}}
                        @if($showResults && strlen($searchLocation) > 2)
                            <div class="absolute z-20 w-full mt-1 bg-white shadow-xl rounded-lg border border-gray-200 max-h-80 overflow-y-auto">
                                @forelse($searchResults as $index => $result)
                                    <button type="button"
                                            wire:click="selectSearchResult('{{ $result['lat'] }}', '{{ $result['lon'] }}', '{{ addslashes($result['display_name']) }}')"
                                            class="w-full text-left px-4 py-3 hover:bg-gray-50 focus:bg-gray-50 border-b border-gray-100 last:border-0 transition duration-150 ease-in-out"
                                            @if($index === 0) x-init="$el.focus()" @endif>
                                        <div class="flex items-start">
                                            {{-- Location Icon based on type --}}
                                            <div class="flex-shrink-0 mr-3">
                                                @if(strpos($result['type'], 'university') !== false || strpos($result['type'], 'college') !== false)
                                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 14l9-5-9-5-9 5 9 5z"></path>
                                                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                                                    </svg>
                                                @elseif(strpos($result['type'], 'city') !== false || strpos($result['type'], 'town') !== false)
                                                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            
                                            {{-- Location Details --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">{{ $result['display_name'] }}</p>
                                                <div class="flex items-center mt-1 space-x-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ ucfirst(str_replace('_', ' ', $result['type'])) }}
                                                    </span>
                                                    @if(isset($result['importance']) && $result['importance'] > 0.5)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                            Popular
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            {{-- Select Indicator --}}
                                            <svg class="h-5 w-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </button>
                                @empty
                                    @if(!$isSearching)
                                        <div class="p-6 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">No locations found for "{{ $searchLocation }}"</p>
                                            <div class="mt-4 space-y-2">
                                                <p class="text-xs text-gray-400">Try:</p>
                                                <ul class="text-xs text-gray-500 space-y-1">
                                                    <li>• Using a more general name (e.g., "Keffi" instead of "Nsuk University Keffi")</li>
                                                    <li>• Checking the spelling</li>
                                                    <li>• Searching for a nearby town or landmark</li>
                                                    <li>• Including the state name (e.g., "Keffi, Nasarawa")</li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endforelse
                            </div>
                        @endif
                    </div>

                    {{-- Map for Location Selection --}}
                    <div class="mt-4" x-data="{ showMap: true }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Fine-tune Location on Map
                            @if($latitude && $longitude)
                                <span class="ml-2 text-xs text-green-600 font-normal">✓ Location selected</span>
                            @endif
                        </label>
                        <div id="create-event-map" class="h-96 rounded-lg border-2 border-gray-300 shadow-sm"></div>
                        
                        {{-- Location Details Card --}}
                        @if($latitude && $longitude)
                            <div class="mt-3 p-4 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-green-500 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-green-800">Selected Location:</p>
                                        <p class="text-sm text-green-700 mt-1">{{ $location_name }}</p>
                                        <div class="mt-2 flex items-center space-x-4 text-xs text-green-600">
                                            <span class="flex items-center">
                                                <span class="font-medium mr-1">Lat:</span> {{ number_format($latitude, 6) }}
                                            </span>
                                            <span class="flex items-center">
                                                <span class="font-medium mr-1">Lng:</span> {{ number_format($longitude, 6) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                <div class="flex">
                                    <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    <p class="text-sm text-yellow-700">
                                        <span class="font-semibold">⚠️ Important:</span> Please click on the map to select the exact location for your event.
                                    </p>
                                </div>
                            </div>
                        @endif
                        
                        @error('latitude') 
                            <div class="mt-2 p-2 bg-red-50 rounded border border-red-200">
                                <p class="text-red-500 text-xs">{{ $message }}</p>
                            </div>
                        @enderror
                        @error('longitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        @error('location_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Event Image --}}
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Event Image (Optional)</label>
                        <div class="mt-1 flex items-center space-x-4">
                            <input type="file" 
                                   wire:model="image" 
                                   id="image"
                                   accept="image/*"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Max file size: 2MB. Supported formats: JPG, PNG, GIF</p>
                        @error('image') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        
                        @if ($image)
                            <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600 mb-2">Image Preview:</p>
                                <img src="{{ $image->temporaryUrl() }}" class="h-32 w-32 object-cover rounded-lg border border-gray-200 shadow-sm">
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('events.feed') }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(!$latitude || !$longitude) disabled title="Please select a location on the map first" @endif>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1-4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            let map;
            let marker = null;
            let mapInitialized = false;

            function initMap() {
                // Prevent multiple initializations
                if (mapInitialized) return;
                
                const mapContainer = document.getElementById('create-event-map');
                if (!mapContainer) return;
                
                // Check if map container has dimensions
                if (mapContainer.clientHeight === 0 || mapContainer.clientWidth === 0) {
                    setTimeout(initMap, 100);
                    return;
                }
                
                // Default to Keffi, Nasarawa, Nigeria
                const defaultLat = 8.8481;
                const defaultLng = 7.8736;
                
                // Clean up existing map instance
                if (window.createEventMap) {
                    window.createEventMap.remove();
                }
                
                // Create map with improved tile layer
                map = L.map('create-event-map', {
                    center: [defaultLat, defaultLng],
                    zoom: 13,
                    zoomControl: true,
                    fadeAnimation: true,
                    markerZoomAnimation: true
                });
                
                window.createEventMap = map;
                
                // Use better tile layer with higher detail
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors',
                    maxZoom: 19,
                    minZoom: 6
                }).addTo(map);

                // Add scale control
                L.control.scale({ imperial: false, metric: true }).addTo(map);

                // Add fullscreen control (optional - you'll need to include leaflet.fullscreen CSS/JS)
                if (L.control.fullscreen) {
                    L.control.fullscreen().addTo(map);
                }

                // If we already have coordinates, set marker
                const latitude = {{ $latitude ?: 'null' }};
                const longitude = {{ $longitude ?: 'null' }};
                const locationName = '{{ $location_name }}';

                if (latitude && longitude) {
                    marker = L.marker([latitude, longitude], {
                        draggable: true,
                        autoPan: true
                    }).addTo(map);
                    
                    marker.bindPopup(locationName).openPopup();
                    map.setView([latitude, longitude], 16);
                    
                    // Handle marker drag
                    marker.on('dragend', function(e) {
                        const position = e.target.getLatLng();
                        reverseGeocode(position.lat, position.lng);
                    });
                }

                // Add click handler to map
                map.on('click', function(e) {
                    const { lat, lng } = e.latlng;
                    reverseGeocode(lat, lng);
                });

                mapInitialized = true;
            }

            // Reverse geocoding function
            async function reverseGeocode(lat, lng) {
                // Show loading state on marker
                if (marker) {
                    marker.setOpacity(0.5);
                }
                
                try {
                    // Show loading notification
                    Livewire.dispatch('notify', { 
                        message: 'Getting location details...', 
                        type: 'loading' 
                    });
                    
                    // Use structured request for better results
                    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&extratags=1&namedetails=1&accept-language=en`;
                    
                    const response = await fetch(url, {
                        headers: {
                            'User-Agent': 'CampusEvents App/1.0',
                            'Accept-Language': 'en-US,en;q=0.9'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    
                    const data = await response.json();
                    
                    // Format the location name intelligently
                    let locationName = formatLocationName(data);
                    
                    // Update Livewire component
                    Livewire.dispatch('setLocation', {
                        latitude: lat,
                        longitude: lng,
                        name: locationName
                    });
                    
                    // Update marker
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                        marker.setOpacity(1);
                    } else {
                        marker = L.marker([lat, lng], {
                            draggable: true,
                            autoPan: true
                        }).addTo(map);
                        
                        // Handle marker drag
                        marker.on('dragend', function(e) {
                            const position = e.target.getLatLng();
                            reverseGeocode(position.lat, position.lng);
                        });
                    }
                    
                    marker.bindPopup(`
                        <div style="min-width: 200px;">
                            <strong style="font-size: 14px;">Selected Location</strong><br>
                            <span style="font-size: 12px;">${locationName}</span><br>
                            <span style="font-size: 10px; color: #666;">Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</span>
                        </div>
                    `).openPopup();
                    
                    Livewire.dispatch('notify', { 
                        message: 'Location set successfully!', 
                        type: 'success' 
                    });
                    
                } catch (error) {
                    console.error('Error getting location name:', error);
                    
                    // Fallback if reverse geocoding fails
                    const locationName = `Location at ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                    
                    Livewire.dispatch('setLocation', {
                        latitude: lat,
                        longitude: lng,
                        name: locationName
                    });
                    
                    if (marker) {
                        marker.setLatLng([lat, lng]);
                        marker.setOpacity(1);
                    } else {
                        marker = L.marker([lat, lng], {
                            draggable: true
                        }).addTo(map);
                    }
                    
                    marker.bindPopup(`
                        <div style="min-width: 200px;">
                            <strong style="font-size: 14px;">Selected Location</strong><br>
                            <span style="font-size: 12px;">${locationName}</span>
                        </div>
                    `).openPopup();
                    
                    Livewire.dispatch('notify', { 
                        message: 'Location set (basic coordinates)', 
                        type: 'warning' 
                    });
                }
            }

            // Format location name from Nominatim response
            function formatLocationName(data) {
                let parts = [];
                
                // Get the most specific name first
                if (data.name) {
                    parts.push(data.name);
                } else if (data.address?.amenity) {
                    parts.push(data.address.amenity);
                } else if (data.address?.university) {
                    parts.push(data.address.university);
                } else if (data.address?.college) {
                    parts.push(data.address.college);
                } else if (data.address?.school) {
                    parts.push(data.address.school);
                }
                
                // Add road/street
                if (data.address?.road && !parts.includes(data.address.road)) {
                    parts.push(data.address.road);
                }
                
                // Add suburb/neighborhood
                if (data.address?.suburb && !parts.includes(data.address.suburb)) {
                    parts.push(data.address.suburb);
                }
                
                // Add town/city
                if (data.address?.town && !parts.includes(data.address.town)) {
                    parts.push(data.address.town);
                } else if (data.address?.city && !parts.includes(data.address.city)) {
                    parts.push(data.address.city);
                }
                
                // Add state
                if (data.address?.state) {
                    parts.push(data.address.state);
                }
                
                // Add country (if not Nigeria, or if we want to show it)
                if (data.address?.country && data.address.country !== 'Nigeria') {
                    parts.push(data.address.country);
                }
                
                if (parts.length > 0) {
                    return parts.join(', ');
                }
                
                // Fallback to display_name
                if (data.display_name) {
                    const nameParts = data.display_name.split(',');
                    return nameParts.slice(0, 4).join(',');
                }
                
                return 'Selected Location';
            }

            // Listen for center map event
            Livewire.on('center-map', (data) => {
                if (!map) {
                    initMap();
                    setTimeout(() => {
                        centerMap(data);
                    }, 200);
                } else {
                    centerMap(data);
                }
            });

            function centerMap(data) {
                if (!map) return;
                
                map.setView([data.latitude, data.longitude], 16, {
                    animate: true,
                    duration: 0.5
                });
                
                if (marker) {
                    marker.setLatLng([data.latitude, data.longitude]);
                } else {
                    marker = L.marker([data.latitude, data.longitude], {
                        draggable: true
                    }).addTo(map);
                }
                
                marker.bindPopup(data.name).openPopup();
            }

            // Listen for notifications
            Livewire.on('notify', (data) => {
                // You can implement a toast notification here
                console.log('Notification:', data.message, data.type);
            });

            // Initialize map when component is ready
            if (document.getElementById('create-event-map')) {
                // Small delay to ensure DOM is ready
                setTimeout(initMap, 100);
            }

            // Cleanup on component unmount
            Livewire.hook('component.removed', (component) => {
                if (component.component?.name === 'create-event' && window.createEventMap) {
                    window.createEventMap.remove();
                    window.createEventMap = null;
                    mapInitialized = false;
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        #create-event-map {
            z-index: 1;
            min-height: 400px;
            border-radius: 0.5rem;
        }
        .leaflet-container {
            font-family: inherit;
            border-radius: 0.5rem;
        }
        .leaflet-popup-content {
            font-size: 13px;
            line-height: 1.4;
            margin: 8px 12px;
        }
        .leaflet-control-attribution {
            font-size: 8px;
            background: rgba(255, 255, 255, 0.8);
            padding: 2px 5px;
            border-radius: 3px;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin {
            animation: spin 1s linear infinite;
        }
        
        /* Custom scrollbar for dropdown */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }
        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Loading state for button */
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Transition effects */
        .transition {
            transition: all 0.2s ease-in-out;
        }
    </style>
    @endpush
</div>