<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventFeed extends Component
{
    use WithPagination;

    public $search = '';
    public $filter = 'latest'; // latest, nearby, upcoming, popular
    public $userLatitude = null;
    public $userLongitude = null;
    public $radius = 5; // 5km radius
    public $dateRange = 'all'; // all, today, this_week, this_month

    protected $queryString = [
        'search' => ['except' => ''],
        'filter' => ['except' => 'latest'],
        'dateRange' => ['except' => 'all'],
    ];

    public function mount()
    {
        // Try to get user's location from session
        if (session()->has('user_latitude') && session()->has('user_longitude')) {
            $this->userLatitude = session('user_latitude');
            $this->userLongitude = session('user_longitude');
        }
    }

    public function setLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;
        
        session([
            'user_latitude' => $latitude,
            'user_longitude' => $longitude
        ]);
        
        $this->resetPage();
        
        $this->dispatch('notify', [
            'message' => 'Location updated successfully!',
            'type' => 'success'
        ]);
    }

    public function clearLocation()
    {
        $this->userLatitude = null;
        $this->userLongitude = null;
        
        session()->forget(['user_latitude', 'user_longitude']);
        
        $this->resetPage();
        
        $this->dispatch('notify', [
            'message' => 'Location cleared.',
            'type' => 'info'
        ]);
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function getEventsProperty()
    {
        $query = Event::with(['user', 'likes', 'attendees', 'comments'])
            ->where('event_date', '>=', now())
            ->where('is_active', true);

        // Apply search
        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location_name', 'like', '%' . $this->search . '%');
            });
        }

        // Apply date range filter
        switch ($this->dateRange) {
            case 'today':
                $query->whereDate('event_date', today());
                break;
            case 'this_week':
                $query->whereBetween('event_date', [now(), now()->addWeek()]);
                break;
            case 'this_month':
                $query->whereBetween('event_date', [now(), now()->addMonth()]);
                break;
        }

        // Apply filters
        switch ($this->filter) {
            case 'nearby':
                if ($this->userLatitude && $this->userLongitude) {
                    $query = $query->nearby(
                        $this->userLatitude, 
                        $this->userLongitude, 
                        $this->radius
                    );
                }
                break;
                
            case 'upcoming':
                $query->orderBy('event_date', 'asc');
                break;
                
            case 'popular':
                $query->withCount('attendees')->orderBy('attendees_count', 'desc');
                break;
                
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate(9);
    }

    public function render()
    {
        return view('livewire.event-feed', [
            'events' => $this->events
        ])->layout('layouts.app');
    }
}