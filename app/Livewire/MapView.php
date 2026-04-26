<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class MapView extends Component
{
    public $userLatitude = null;
    public $userLongitude = null;
    public $selectedEvent = null;
    public $radius = 5; // 5km radius
    public $mapCenter = ['lat' => 8.8481, 'lng' => 7.8736]; // Default to Keffi, Nigeria
    public $mapZoom = 12;

    public function mount()
    {
        // Try to get user's location from session
        if (session()->has('user_latitude') && session()->has('user_longitude')) {
            $this->userLatitude = session('user_latitude');
            $this->userLongitude = session('user_longitude');
            $this->mapCenter = [
                'lat' => $this->userLatitude,
                'lng' => $this->userLongitude
            ];
        }
    }

    public function setLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        $this->mapCenter = [
            'lat' => $latitude,
            'lng' => $longitude
        ];
        
        session([
            'user_latitude' => $latitude,
            'user_longitude' => $longitude
        ]);
        
        $this->dispatch('location-updated', [
            'lat' => $latitude,
            'lng' => $longitude
        ]);
        
        $this->dispatch('notify', [
            'message' => 'Location updated successfully!',
            'type' => 'success'
        ]);
    }

    public function selectEvent($eventId)
    {
        $this->selectedEvent = Event::with(['user', 'likes', 'attendees'])->find($eventId);
        
        if ($this->selectedEvent) {
            $this->dispatch('event-selected', [
                'lat' => $this->selectedEvent->latitude,
                'lng' => $this->selectedEvent->longitude
            ]);
        }
    }

    public function getEventsProperty()
    {
        $query = Event::with(['user', 'likes', 'attendees'])
            ->where('event_date', '>=', now())
            ->where('is_active', true);

        // Get nearby events if we have user location
        if ($this->userLatitude && $this->userLongitude) {
            $query = $query->nearby(
                $this->userLatitude, 
                $this->userLongitude, 
                $this->radius
            );
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.map-view', [
            'events' => $this->events
        ])->layout('layouts.app');
    }
}