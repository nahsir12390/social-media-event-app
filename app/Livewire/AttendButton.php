<?php
// app/Livewire/AttendButton.php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;

class AttendButton extends Component
{
    public Event $event;
    public $attendeesCount;
    public $isAttending;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->attendeesCount = $event->attendees()->where('status', 'attending')->count();
        $this->isAttending = $event->isAttendedBy(auth()->user());
    }

    public function toggleAttendance()
    {
        $attendance = $this->event->attendees()
            ->where('user_id', auth()->id())
            ->first();

        if ($attendance) {
            if ($attendance->status === 'attending') {
                $attendance->delete();
                $this->isAttending = false;
                $this->attendeesCount--;
            }
        } else {
            $this->event->attendees()->create([
                'user_id' => auth()->id(),
                'status' => 'attending'
            ]);
            $this->isAttending = true;
            $this->attendeesCount++;
        }

        $this->dispatch('attendance-toggled');
    }

    public function render()
    {
        return view('livewire.attend-button');
    }
}