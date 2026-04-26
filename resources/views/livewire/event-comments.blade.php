{{-- resources/views/livewire/event-comments.blade.php --}}

<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Comments ({{ $comments->count() }})</h3>

    {{-- Add Comment Form --}}
    <form wire:submit="addComment" class="mb-6">
        <div class="flex items-start space-x-3">
            @if(auth()->user()->profile_photo)
                <img src="{{ Storage::url(auth()->user()->profile_photo) }}" 
                     class="w-10 h-10 rounded-full object-cover">
            @else
                <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </span>
                </div>
            @endif
            
            <div class="flex-1">
                <textarea wire:model="newComment" 
                          rows="2"
                          placeholder="Add a comment..."
                          class="block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm"></textarea>
                @error('newComment') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                
                <div class="mt-2 flex justify-end">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Post Comment
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Comments List --}}
    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="flex space-x-3" wire:key="comment-{{ $comment->id }}">
                @if($comment->user->profile_photo)
                    <img src="{{ Storage::url($comment->user->profile_photo) }}" 
                         class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="w-8 h-8 rounded-full bg-green-600 flex items-center justify-center">
                        <span class="text-xs font-medium text-white">
                            {{ substr($comment->user->name, 0, 1) }}
                        </span>
                    </div>
                @endif
                
                <div class="flex-1">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-700 mt-1">{{ $comment->comment }}</p>
                    </div>
                    
                    {{-- Delete button for own comments or admin --}}
                    @if($comment->user_id === auth()->id() || auth()->user()->is_admin)
                        <button wire:click="deleteComment({{ $comment->id }})" 
                                wire:confirm="Are you sure you want to delete this comment?"
                                class="text-xs text-red-600 hover:text-red-800 mt-1">
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-center py-4">No comments yet. Be the first to comment!</p>
        @endforelse
    </div>
</div>