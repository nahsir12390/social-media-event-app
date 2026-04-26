<?php
// app/Livewire/LikeButton.php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class LikeButton extends Component
{
    public Event $event;
    public $likesCount;
    public $isLiked;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->likesCount = $event->likes()->count();
        $this->isLiked = $event->isLikedBy(auth()->user());
    }

    public function toggleLike()
    {
        if ($this->isLiked) {
            $this->event->likes()->where('user_id', auth()->id())->delete();
            $this->isLiked = false;
            $this->likesCount--;
        } else {
            $this->event->likes()->create(['user_id' => auth()->id()]);
            $this->isLiked = true;
            $this->likesCount++;
        }

        $this->dispatch('like-toggled');
    }

    public function render()
    {
        return view('livewire.like-button');
    }
}