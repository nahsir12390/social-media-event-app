{{-- resources/views/livewire/event-details.blade.php --}}

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- Back Button --}}
        <div class="mb-6">
            <a href="{{ route('events.feed') }}" class="text-indigo-600 hover:text-indigo-800 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Events
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Event Image --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @if($event->image)
                        <img src="{{ Storage::url($event->image) }}" 
                             alt="{{ $event->title }}"
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white text-6xl font-bold">{{ substr($event->title, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Event Details --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                        
                        @if($isOwner)
                            <div class="flex space-x-2">
                                <a href="{{ route('events.edit', $event) }}" 
                                   class="text-indigo-600 hover:text-indigo-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <button wire:click="deleteEvent" 
                                        wire:confirm="Are you sure you want to delete this event?"
                                        class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    
                    <div class="prose max-w-none mb-6">
                        <p class="text-gray-700">{{ $event->description }}</p>
                    </div>

                    {{-- Action Buttons using Livewire components --}}
                    <div class="flex space-x-4 border-t border-gray-200 pt-6">
                        <livewire:like-button :event="$event" :key="'like-'.$event->id" />
                        <livewire:attend-button :event="$event" :key="'attend-'.$event->id" />
                    </div>
                </div>

                {{-- Comments Section --}}
                <livewire:event-comments :event="$event" :key="'comments-'.$event->id" />
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Event Info Card --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Event Details</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date & Time</p>
                                <p class="text-gray-900">{{ $event->event_date->format('l, F j, Y') }}</p>
                                <p class="text-gray-600">{{ $event->event_date->format('g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Location</p>
                                <p class="text-gray-900">{{ $event->location_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Hosted by</p>
                                <div class="flex items-center mt-1">
                                    @if($event->user->profile_photo)
                                        <img src="{{ Storage::url($event->user->profile_photo) }}" 
                                             class="w-8 h-8 rounded-full mr-2 object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center mr-2">
                                            <span class="text-sm font-medium text-white">
                                                {{ substr($event->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <a href="{{ route('profile.show', $event->user) }}" class="text-gray-900 hover:text-indigo-600">
                                        {{ $event->user->name }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Mini Map --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Location Map</h3>
                    <div id="event-map" class="h-64 rounded-lg"></div>
                </div>

                {{-- Attendees List --}}
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Attendees ({{ $event->attendees->where('status', 'attending')->count() }})</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        @forelse($event->attendees->where('status', 'attending')->take(10) as $attendee)
                            <div class="flex items-center">
                                @if($attendee->user->profile_photo)
                                    <img src="{{ Storage::url($attendee->user->profile_photo) }}" 
                                         class="w-8 h-8 rounded-full mr-3 object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center mr-3">
                                        <span class="text-xs font-medium text-white">
                                            {{ substr($attendee->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="text-sm text-gray-900">{{ $attendee->user->name }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No attendees yet. Be the first!</p>
                        @endforelse
                        
                        @if($event->attendees->where('status', 'attending')->count() > 10)
                            <button class="text-indigo-600 text-sm hover:text-indigo-800">
                                View all {{ $event->attendees->where('status', 'attending')->count() }} attendees
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            // Initialize map for event location
            const map = L.map('event-map').setView([{{ $event->latitude }}, {{ $event->longitude }}], 15);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.marker([{{ $event->latitude }}, {{ $event->longitude }}])
                .addTo(map)
                .bindPopup("{{ $event->location_name }}")
                .openPopup();
        });
    </script>
</div>