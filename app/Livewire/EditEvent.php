<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class EditEvent extends Component
{
    use WithFileUploads;

    public $event;
    public $title;
    public $description;
    public $event_date;
    public $location_name;
    public $latitude;
    public $longitude;
    public $image;
    public $newImage;
    public $searchLocation = '';
    public $searchResults = [];
    public $showResults = false;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'required|min:10',
        'event_date' => 'required|date|after:now',
        'location_name' => 'required|max:255',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'newImage' => 'nullable|image|max:2048', // 2MB max
    ];

    public function mount(Event $event)
    {
        // Check if user is the owner
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->event = $event;
        $this->title = $event->title;
        $this->description = $event->description;
        $this->event_date = $event->event_date->format('Y-m-d\TH:i');
        $this->location_name = $event->location_name;
        $this->latitude = $event->latitude;
        $this->longitude = $event->longitude;
        $this->image = $event->image;
    }

    public function updatedSearchLocation()
    {
        if (strlen($this->searchLocation) > 2) {
            $this->searchLocations();
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function searchLocations()
    {
        // Clear previous results
        $this->searchResults = [];
        
        // Add Nigeria bias to the search
        $query = urlencode($this->searchLocation . ', Nigeria');
        
        // Using Nominatim with proper user agent
        $url = "https://nominatim.openstreetmap.org/search?format=json&q={$query}&limit=5&countrycodes=ng&addressdetails=1";
        
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'CampusEvents App/1.0');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $results = json_decode($response, true);
                
                if (is_array($results)) {
                    foreach ($results as $result) {
                        $displayName = $this->formatLocationName($result);
                        
                        $this->searchResults[] = [
                            'lat' => $result['lat'],
                            'lon' => $result['lon'],
                            'display_name' => $displayName,
                            'full_address' => $result['display_name'] ?? ''
                        ];
                    }
                    $this->showResults = count($this->searchResults) > 0;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
        }
    }

    private function formatLocationName($result)
    {
        $parts = [];
        
        if (isset($result['address'])) {
            $address = $result['address'];
            
            if (isset($address['road'])) $parts[] = $address['road'];
            if (isset($address['city'])) $parts[] = $address['city'];
            if (isset($address['town'])) $parts[] = $address['town'];
            if (isset($address['village'])) $parts[] = $address['village'];
            if (isset($address['state'])) $parts[] = $address['state'];
            if (isset($address['country'])) $parts[] = $address['country'];
        }
        
        if (!empty($parts)) {
            return implode(', ', $parts);
        }
        
        $nameParts = explode(',', $result['display_name']);
        return implode(',', array_slice($nameParts, 0, 4));
    }

    public function selectSearchResult($lat, $lon, $name)
    {
        $this->latitude = $lat;
        $this->longitude = $lon;
        $this->location_name = $name;
        $this->searchResults = [];
        $this->showResults = false;
        $this->searchLocation = $name;
        
        $this->dispatch('center-map', latitude: $lat, longitude: $lon, name: $name);
    }

    public function setLocation($latitude, $longitude, $name)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->location_name = $name;
    }

    public function update()
    {
        $this->validate();

        $imagePath = $this->event->image;
        
        if ($this->newImage) {
            // Delete old image if exists
            if ($this->event->image) {
                \Storage::disk('public')->delete($this->event->image);
            }
            $imagePath = $this->newImage->store('event-images', 'public');
        }

        $this->event->update([
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'location_name' => $this->location_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image' => $imagePath,
        ]);

        session()->flash('message', 'Event updated successfully!');

        return redirect()->route('events.show', $this->event);
    }

    public function render()
    {
        return view('livewire.edit-event')->layout('layouts.app');
    }
}