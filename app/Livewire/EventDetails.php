<?php
// app/Livewire/EventDetails.php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventDetails extends Component
{
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event->load(['user', 'likes', 'comments.user', 'attendees.user']);
    }

    public function deleteEvent()
    {
        if ($this->event->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403);
        }

        $this->event->delete();
        
        session()->flash('message', 'Event deleted successfully.');
        
        return redirect()->route('events.feed');
    }

    public function render()
    {
        return view('livewire.event-details', [
            'isOwner' => $this->event->user_id === auth()->id(),
        ])->layout('layouts.app');
    }
}