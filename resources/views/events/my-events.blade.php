{{-- resources/views/events/my-events.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Events') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Events I Created --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Events I've Created</h3>
                    
                    @if($createdEvents->count() > 0)
                        <div class="space-y-4">
                            @foreach($createdEvents as $event)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                    <div class="flex items-center space-x-4">
                                        @if($event->image)
                                            <img src="{{ Storage::url($event->image) }}" 
                                                 class="w-16 h-16 object-cover rounded-lg">
                                        @else
                                            <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-lg flex items-center justify-center">
                                                <span class="text-white text-xl font-bold">{{ substr($event->title, 0, 1) }}</span>
                                            </div>
                                        @endif
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $event->title }}</h4>
                                            <p class="text-sm text-gray-500">{{ $event->event_date->format('M d, Y g:i A') }}</p>
                                            <div class="flex items-center space-x-3 mt-1 text-xs text-gray-400">
                                                <span>{{ $event->likes->count() }} likes</span>
                                                <span>•</span>
                                                <span>{{ $event->attendees->where('status', 'attending')->count() }} attending</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('events.show', $event) }}" 
                                           class="text-green-600 hover:text-green-800 text-sm px-3 py-1">
                                            View
                                        </a>
                                        <a href="{{ route('events.edit', $event) }}" 
                                           class="text-gray-600 hover:text-gray-800 text-sm px-3 py-1">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $createdEvents->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">You haven't created any events yet.</p>
                        <a href="{{ route('events.create') }}" class="inline-block mt-3 text-green-600 hover:text-green-800">
                            Create your first event →
                        </a>
                    @endif
                </div>
            </div>

            {{-- Events I'm Attending --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Events I'm Attending</h3>
                    
                    @if($attendingEvents->count() > 0)
                        <div class="space-y-4">
                            @foreach($attendingEvents as $event)
                                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                    <div class="flex items-center space-x-4">
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
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $event->title }}</h4>
                                            <p class="text-sm text-gray-500">{{ $event->event_date->format('M d, Y g:i A') }}</p>
                                            <p class="text-xs text-gray-400">Hosted by {{ $event->user->name }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('events.show', $event) }}" 
                                       class="text-green-600 hover:text-green-800 text-sm px-3 py-1">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $attendingEvents->links() }}
                        </div>
                    @else
                        <p class="text-gray-500">You're not attending any upcoming events.</p>
                        <a href="{{ route('events.feed') }}" class="inline-block mt-3 text-green-600 hover:text-green-800">
                            Browse events →
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>