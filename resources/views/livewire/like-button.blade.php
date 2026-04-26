

<button wire:click="toggleLike" 
        class="inline-flex items-center px-3 py-2 rounded-md text-sm font-medium transition {{ $isLiked ? 'bg-red-50 text-red-600' : 'bg-gray-50 text-gray-700 hover:bg-red-50 hover:text-red-600' }}">
    <svg class="w-5 h-5 mr-1.5" fill="{{ $isLiked ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
    </svg>
    <span>{{ $likesCount }} {{ $likesCount === 1 ? 'Like' : 'Likes' }}</span>
</button>