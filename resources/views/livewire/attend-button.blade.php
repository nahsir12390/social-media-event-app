

<button wire:click="toggleAttendance" 
        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium transition {{ $isAttending ? 'bg-green-50 text-green-600 border border-green-200' : 'bg-green-600 text-white hover:bg-green-700' }}">
    @if($isAttending)
        <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>Attending ({{ $attendeesCount }})</span>
    @else
        <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        <span>Attend ({{ $attendeesCount }})</span>
    @endif
</button>