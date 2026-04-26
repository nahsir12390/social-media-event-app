<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Event</h2>

                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit="update" class="space-y-6">
                    {{-- Title --}}
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Event Title</label>
                        <input type="text" 
                               wire:model="title" 
                               id="title"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea wire:model="description" 
                                  id="description" 
                                  rows="5"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"></textarea>
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
                    <div class="relative">
                        <label for="searchLocation" class="block text-sm font-medium text-gray-700">Search Location</label>
                        <div class="mt-1">
                            <input type="text" 
                                   wire:model.live.debounce.500ms="searchLocation" 
                                   id="searchLocation"
                                   placeholder="Search for a location in Nigeria"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500">
                        </div>
                        
                        {{-- Search Results Dropdown --}}
                        @if($showResults && count($searchResults) > 0)
                            <div class="absolute z-10 w-full mt-1 bg-white shadow-lg rounded-md border border-gray-200 max-h-60 overflow-y-auto">
                                @foreach($searchResults as $result)
                                    <button type="button"
                                            wire:click="selectSearchResult('{{ $result['lat'] }}', '{{ $result['lon'] }}', '{{ $result['display_name'] }}')"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 focus:bg-gray-100 border-b border-gray-100 last:border-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $result['display_name'] }}</p>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Map for Location Selection --}}
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fine-tune Location on Map</label>
                        <div id="edit-event-map" class="h-96 rounded-lg border-2 border-gray-300"></div>
                        
                        @if($latitude && $longitude)
                            <div class="mt-2 p-3 bg-green-50 rounded-md">
                                <p class="text-sm text-green-700">
                                    <span class="font-semibold">Selected Location:</span> {{ $location_name }}
                                </p>
                                <p class="text-xs text-green-600 mt-1">
                                    Lat: {{ $latitude }}, Lng: {{ $longitude }}
                                </p>
                            </div>
                        @endif
                        
                        @error('latitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        @error('longitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Event Image --}}
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700">Event Image</label>
                        
                        @if($image)
                            <div class="mt-2 mb-2">
                                <img src="{{ Storage::url($image) }}" class="h-32 w-32 object-cover rounded-lg">
                            </div>
                        @endif
                        
                        <input type="file" 
                               wire:model="newImage" 
                               id="image"
                               accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                        @error('newImage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        
                        @if ($newImage)
                            <div class="mt-2">
                                <p class="text-sm text-gray-600 mb-1">New Image Preview:</p>
                                <img src="{{ $newImage->temporaryUrl() }}" class="h-32 w-32 object-cover rounded-lg">
                            </div>
                        @endif
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('events.show', $event) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                            Update Event
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
                if (mapInitialized) return;
                
                const mapContainer = document.getElementById('edit-event-map');
                if (!mapContainer) return;
                
                if (mapContainer.clientHeight === 0 || mapContainer.clientWidth === 0) {
                    setTimeout(initMap, 100);
                    return;
                }
                
                const defaultLat = {{ $latitude ?: 8.8481 }};
                const defaultLng = {{ $longitude ?: 7.8736 }};
                
                if (window.editEventMap) {
                    window.editEventMap.remove();
                }
                
                map = L.map('edit-event-map').setView([defaultLat, defaultLng], 13);
                window.editEventMap = map;
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                const latitude = {{ $latitude ?: 'null' }};
                const longitude = {{ $longitude ?: 'null' }};
                const locationName = '{{ $location_name }}';

                if (latitude && longitude) {
                    marker = L.marker([latitude, longitude]).addTo(map);
                    marker.bindPopup(locationName).openPopup();
                    map.setView([latitude, longitude], 16);
                }

                map.on('click', async function(e) {
                    const { lat, lng } = e.latlng;
                    
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`,
                            {
                                headers: {
                                    'User-Agent': 'CampusEvents App/1.0'
                                }
                            }
                        );
                        const data = await response.json();
                        
                        let locationName = 'Selected Location';
                        if (data.display_name) {
                            const parts = data.display_name.split(',');
                            locationName = parts.slice(0, 4).join(',');
                        }
                        
                        Livewire.dispatch('setLocation', {
                            latitude: lat,
                            longitude: lng,
                            name: locationName
                        });
                        
                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng]).addTo(map);
                        }
                        
                        marker.bindPopup(locationName).openPopup();
                        
                    } catch (error) {
                        console.error('Error getting location name:', error);
                        
                        const locationName = `Location at ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                        
                        Livewire.dispatch('setLocation', {
                            latitude: lat,
                            longitude: lng,
                            name: locationName
                        });
                        
                        if (marker) {
                            marker.setLatLng([lat, lng]);
                        } else {
                            marker = L.marker([lat, lng]).addTo(map);
                        }
                        
                        marker.bindPopup(locationName).openPopup();
                    }
                });

                mapInitialized = true;
            }

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
                
                map.setView([data.latitude, data.longitude], 16);
                
                if (marker) {
                    marker.setLatLng([data.latitude, data.longitude]);
                } else {
                    marker = L.marker([data.latitude, data.longitude]).addTo(map);
                }
                
                marker.bindPopup(data.name).openPopup();
            }

            if (document.getElementById('edit-event-map')) {
                setTimeout(initMap, 100);
            }

            Livewire.hook('component.removed', (component) => {
                if (component.name === 'edit-event' && window.editEventMap) {
                    window.editEventMap.remove();
                    window.editEventMap = null;
                    mapInitialized = false;
                }
            });
        });
    </script>
    @endpush

    @push('styles')
    <style>
        #edit-event-map {
            z-index: 1;
            min-height: 400px;
        }
    </style>
    @endpush
</div>