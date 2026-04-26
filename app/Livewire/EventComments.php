<?php
// app/Livewire/EventComments.php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventComments extends Component
{
    public Event $event;
    public $newComment = '';
    public $comments;

    protected $rules = [
        'newComment' => 'required|min:3|max:500',
    ];

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = $this->event->comments()
            ->with('user')
            ->latest()
            ->take(50)
            ->get();
    }

    public function addComment()
    {
        $this->validate();

        $this->event->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->loadComments();
        
        $this->dispatch('comment-added');
    }

    public function deleteComment($commentId)
    {
        $comment = $this->event->comments()->find($commentId);
        
        if ($comment && ($comment->user_id === auth()->id() || auth()->user()->is_admin)) {
            $comment->delete();
            $this->loadComments();
            $this->dispatch('comment-deleted');
        }
    }

    public function render()
    {
        return view('livewire.event-comments');
    }
}