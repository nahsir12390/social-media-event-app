<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">{{ $user->name }}</h2>
    </x-slot>
    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold">{{ $user->name }}</h1>
            <p class="mt-2 text-gray-600">{{ $user->bio }}</p>
        </div>
        <h2 class="text-xl font-semibold">Events by {{ $user->name }}</h2>
        @forelse ($events as $event)
            <a href="{{ route('events.show', $event) }}" class="block bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-indigo-600">{{ $event->title }}</h3>
                <p class="text-gray-600">{{ $event->event_date->format('M j, Y g:i A') }} · {{ $event->location_name }}</p>
            </a>
        @empty
            <p>No events yet.</p>
        @endforelse
        {{ $events->links() }}
    </div>
</x-app-layout>
