<?php

namespace App\Livewire;

use App\Models\Event;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateEvent extends Component
{
    use WithFileUploads;

    public $title = '';
    public $description = '';
    public $event_date = '';
    public $location_name = '';
    public $latitude = '';
    public $longitude = '';
    public $image;
    public $searchLocation = '';
    public $searchResults = [];
    public $showResults = false;
    public $isSearching = false;

    protected $rules = [
        'title' => 'required|min:3|max:255',
        'description' => 'required|min:10',
        'event_date' => 'required|date|after:now',
        'location_name' => 'required|max:255',
        'latitude' => 'required|numeric|between:-90,90',
        'longitude' => 'required|numeric|between:-180,180',
        'image' => 'nullable|image|max:2048', // 2MB max
    ];

    protected $messages = [
        'title.required' => 'Please enter an event title.',
        'title.min' => 'Title must be at least 3 characters.',
        'description.required' => 'Please enter an event description.',
        'description.min' => 'Description must be at least 10 characters.',
        'event_date.required' => 'Please select an event date and time.',
        'event_date.after' => 'Event date must be in the future.',
        'location_name.required' => 'Please select a location on the map.',
        'latitude.required' => 'Please select a location on the map.',
        'longitude.required' => 'Please select a location on the map.',
        'image.max' => 'Image size must not exceed 2MB.',
    ];

    public function mount()
    {
        // Set default date to tomorrow
        $this->event_date = now()->addDay()->format('Y-m-d\TH:i');
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
        $this->isSearching = true;
        $this->searchResults = [];
        
        // Try multiple search strategies with structured parameters
        $results = [];
        
        // Strategy 1: Direct simple search first (most reliable)
        $simpleResults = $this->simpleSearch($this->searchLocation);
        $results = array_merge($results, $simpleResults);
        
        // Strategy 2: Extract components and use structured query
        if (empty($results)) {
            $structuredResults = $this->structuredSearch($this->searchLocation);
            $results = array_merge($results, $structuredResults);
        }
        
        // Strategy 3: If no results, try free-form query with Nigeria bias
        if (empty($results)) {
            $freeFormResults = $this->freeFormSearch($this->searchLocation . ', Nigeria');
            $results = array_merge($results, $freeFormResults);
        }
        
        // Strategy 4: For educational institutions, try amenity-specific search
        if (empty($results) && $this->isEducationalQuery($this->searchLocation)) {
            $eduResults = $this->searchEducationalInstitutions($this->searchLocation);
            $results = array_merge($results, $eduResults);
        }
        
        // Strategy 5: Try with viewbox bias around Nasarawa state
        if (empty($results)) {
            $biasedResults = $this->searchWithLocationBias($this->searchLocation);
            $results = array_merge($results, $biasedResults);
        }
        
        // Remove duplicates based on coordinates
        $results = $this->removeDuplicateResults($results);
        
        // Sort by importance/relevance
        $results = $this->sortResultsByRelevance($results, $this->searchLocation);
        
        // Limit to 10 results
        $this->searchResults = array_slice($results, 0, 10);
        $this->showResults = true;
        $this->isSearching = false;
        
        Log::info('Search results for "' . $this->searchLocation . '":', ['count' => count($this->searchResults), 'strategies_used' => 5]);
    }

    /**
     * Simple direct search (most reliable)
     */
    private function simpleSearch($query)
    {
        $params = [
            'format' => 'json',
            'q' => $query,
            'limit' => 10,
            'addressdetails' => 1,
            'extratags' => 1,
            'namedetails' => 1,
        ];
        
        $url = "https://nominatim.openstreetmap.org/search?" . http_build_query($params);
        return $this->executeNominatimRequest($url);
    }

    /**
     * Structured search using address components for maximum accuracy
     */
    private function structuredSearch($query)
    {
        $results = [];
        
        // Parse the query to identify components
        $components = $this->parseQueryComponents($query);
        
        // Build structured parameters
        $params = [
            'format' => 'json',
            'limit' => 10,
            'addressdetails' => 1,
            'countrycodes' => 'ng', // Strict filter for Nigeria
            'dedupe' => 1,
            'extratags' => 1, // Get additional tags for better identification
            'namedetails' => 1, // Get all names (local, international, etc.)
            'accept-language' => 'en', // Prefer English results
        ];
        
        // Add structured parameters if components were found
        if (!empty($components['city'])) {
            $params['city'] = $components['city'];
        }
        if (!empty($components['state'])) {
            $params['state'] = $components['state'];
        }
        if (!empty($components['amenity'])) {
            $params['amenity'] = $components['amenity'];
        }
        if (!empty($components['street'])) {
            $params['street'] = $components['street'];
        }
        
        // If we have specific components, use structured format
        if (count($params) > 5) { // More than just the default params
            $url = "https://nominatim.openstreetmap.org/search?" . http_build_query($params);
            $results = $this->executeNominatimRequest($url);
        }
        
        return $results;
    }

    /**
     * Free-form search with country restriction
     */
    private function freeFormSearch($query)
    {
        $params = [
            'format' => 'json',
            'q' => $query,
            'limit' => 10,
            'addressdetails' => 1,
            'countrycodes' => 'ng',
            'dedupe' => 1,
            'extratags' => 1,
            'namedetails' => 1,
            'accept-language' => 'en',
        ];
        
        $url = "https://nominatim.openstreetmap.org/search?" . http_build_query($params);
        return $this->executeNominatimRequest($url);
    }

    /**
     * Specialized search for educational institutions
     */
    private function searchEducationalInstitutions($query)
    {
        $results = [];
        $components = $this->parseQueryComponents($query);
        
        // Try different amenity types for educational institutions
        $amenityTypes = ['university', 'college', 'school'];
        
        foreach ($amenityTypes as $amenity) {
            $params = [
                'format' => 'json',
                'amenity' => $amenity,
                'limit' => 5,
                'addressdetails' => 1,
                'countrycodes' => 'ng',
                'dedupe' => 1,
                'extratags' => 1,
                'namedetails' => 1,
                'accept-language' => 'en',
            ];
            
            // Add city if found
            if (!empty($components['city'])) {
                $params['city'] = $components['city'];
            }
            
            // Add the institution name if it looks like a proper name
            if (!empty($components['name'])) {
                $params['q'] = $components['name'];
            }
            
            $url = "https://nominatim.openstreetmap.org/search?" . http_build_query($params);
            $searchResults = $this->executeNominatimRequest($url);
            $results = array_merge($results, $searchResults);
        }
        
        return $results;
    }

    /**
     * Search with location bias using viewbox around Nasarawa
     */
    private function searchWithLocationBias($query)
    {
        // Bounding box around Nasarawa state (approximate)
        // Format: left,top,right,bottom
        $viewbox = '6.5,9.5,9.5,7.5'; // Rough bounding box for Nasarawa
        
        $params = [
            'format' => 'json',
            'q' => $query,
            'limit' => 10,
            'addressdetails' => 1,
            'countrycodes' => 'ng',
            'viewbox' => $viewbox,
            'bounded' => 1, // Prioritize results within viewbox
            'dedupe' => 1,
            'extratags' => 1,
            'namedetails' => 1,
            'accept-language' => 'en',
        ];
        
        $url = "https://nominatim.openstreetmap.org/search?" . http_build_query($params);
        return $this->executeNominatimRequest($url);
    }

    /**
     * Execute Nominatim API request with proper error handling
     */
    private function executeNominatimRequest($url)
    {
        $results = [];
        
        try {
            // Create stream context with proper headers and timeout
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 10,
                    'header' => [
                        'User-Agent: CampusEvents App/1.0',
                        'Accept-Language: en-US,en;q=0.9',
                        'Accept: application/json'
                    ]
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false
                ]
            ]);
            
            // Execute request with error handling
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                Log::warning('Nominatim API request failed for URL: ' . $url);
                // Try alternative approach with curl as fallback
                return $this->executeNominatimRequestCurl($url);
            }
            
            // Decode JSON response
            $apiResults = json_decode($response, true);
            
            if (is_array($apiResults) && !empty($apiResults)) {
                foreach ($apiResults as $result) {
                    $results[] = $this->formatNominatimResult($result);
                }
            } else {
                Log::info('No results from Nominatim for URL: ' . $url);
            }
            
        } catch (\Exception $e) {
            Log::error('Nominatim API error: ' . $e->getMessage());
        }
        
        return $results;
    }

    /**
     * Fallback curl method for Nominatim API
     */
    private function executeNominatimRequestCurl($url)
    {
        $results = [];
        
        try {
            if (!function_exists('curl_init')) {
                Log::warning('CURL not available, skipping fallback');
                return $results;
            }
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERAGENT => 'CampusEvents App/1.0',
                CURLOPT_TIMEOUT => 10,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTPHEADER => [
                    'Accept-Language: en-US,en;q=0.9',
                    'Accept: application/json'
                ]
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            
            curl_close($ch);
            
            if (!empty($error)) {
                Log::error('CURL Error: ' . $error);
                return $results;
            }
            
            if ($httpCode === 200 && !empty($response)) {
                $apiResults = json_decode($response, true);
                
                if (is_array($apiResults) && !empty($apiResults)) {
                    foreach ($apiResults as $result) {
                        $results[] = $this->formatNominatimResult($result);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('CURL fallback error: ' . $e->getMessage());
        }
        
        return $results;
    }

    /**
     * Parse query to extract address components
     */
    private function parseQueryComponents($query)
    {
        $components = [
            'amenity' => null,
            'name' => null,
            'city' => null,
            'state' => null,
            'street' => null,
        ];
        
        $lowerQuery = strtolower($query);
        
        // Detect educational institutions
        if (preg_match('/(university|college|school|polytechnic|institute)/i', $lowerQuery, $matches)) {
            $components['amenity'] = strtolower($matches[0]);
        }
        
        // Extract potential city names
        $knownCities = ['keffi', 'akwanga', 'lafia', 'nasawara', 'nasarawa', 'abuja', 'karshi'];
        foreach ($knownCities as $city) {
            if (strpos($lowerQuery, $city) !== false) {
                $components['city'] = $city;
                break;
            }
        }
        
        // Extract state
        if (strpos($lowerQuery, 'nasarawa') !== false || strpos($lowerQuery, 'nasawara') !== false) {
            $components['state'] = 'Nasarawa';
        }
        
        // Extract potential institution name (words before university/college)
        if (preg_match('/([a-z\s]+)\s+(university|college|school)/i', $query, $matches)) {
            $components['name'] = trim($matches[1]);
        }
        
        return $components;
    }

    /**
     * Check if query is likely searching for an educational institution
     */
    private function isEducationalQuery($query)
    {
        $educationalTerms = ['university', 'college', 'school', 'polytechnic', 'institute', 'campus', 'nsuk', 'edu'];
        $lowerQuery = strtolower($query);
        
        foreach ($educationalTerms as $term) {
            if (strpos($lowerQuery, $term) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Format Nominatim API result into a standardized format
     */
    private function formatNominatimResult($result)
    {
        // Build a comprehensive display name
        $displayParts = [];
        
        // Get the main name (prioritize specific names)
        if (!empty($result['name'])) {
            $displayParts[] = $result['name'];
        } elseif (!empty($result['namedetails']['name:en'])) {
            $displayParts[] = $result['namedetails']['name:en'];
        }
        
        // Add address components
        if (isset($result['address'])) {
            $address = $result['address'];
            
            // Add road/street if available and not redundant
            if (isset($address['road']) && !in_array($address['road'], $displayParts)) {
                $displayParts[] = $address['road'];
            }
            
            // Add suburb/neighborhood
            if (isset($address['suburb']) && !in_array($address['suburb'], $displayParts)) {
                $displayParts[] = $address['suburb'];
            }
            
            // Add town/city
            if (isset($address['town']) && !in_array($address['town'], $displayParts)) {
                $displayParts[] = $address['town'];
            } elseif (isset($address['city']) && !in_array($address['city'], $displayParts)) {
                $displayParts[] = $address['city'];
            }
            
            // Add state
            if (isset($address['state'])) {
                $displayParts[] = $address['state'];
            }
            
            // Add country (usually Nigeria)
            if (isset($address['country'])) {
                $displayParts[] = $address['country'];
            }
        }
        
        // If no display parts, use display_name
        if (empty($displayParts)) {
            $nameParts = explode(',', $result['display_name']);
            $displayParts = array_slice($nameParts, 0, 4);
        }
        
        // Determine the type for badge display
        $type = $result['type'] ?? $result['class'] ?? 'location';
        if (isset($result['amenity'])) {
            $type = $result['amenity'];
        } elseif (isset($result['building'])) {
            $type = $result['building'];
        }
        
        return [
            'lat' => $result['lat'],
            'lon' => $result['lon'],
            'display_name' => implode(', ', array_unique($displayParts)),
            'full_address' => $result['display_name'] ?? '',
            'type' => $type,
            'importance' => $result['importance'] ?? 0,
            'class' => $result['class'] ?? '',
            'osm_id' => $result['osm_id'] ?? null,
            'osm_type' => $result['osm_type'] ?? null,
        ];
    }

    /**
     * Extract keywords from search query
     */
    private function extractKeywords($query)
    {
        $commonWords = ['the', 'in', 'at', 'on', 'for', 'to', 'of', 'and', 'near', 'around', 'campus'];
        $words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', strtolower($query)));
        
        $keywords = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $commonWords)) {
                $keywords[] = $word;
            }
        }
        
        // Also try combinations
        if (count($keywords) >= 2) {
            $keywords[] = $keywords[0] . ' ' . $keywords[1];
        }
        
        return array_unique($keywords);
    }

    /**
     * Remove duplicate results based on coordinates
     */
    private function removeDuplicateResults($results)
    {
        $unique = [];
        $seen = [];
        
        foreach ($results as $result) {
            $key = round($result['lat'], 4) . ',' . round($result['lon'], 4);
            
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = $result;
            }
        }
        
        return $unique;
    }

    /**
     * Sort results by relevance to search query
     */
    private function sortResultsByRelevance($results, $searchQuery)
    {
        $searchTerms = explode(' ', strtolower($searchQuery));
        
        usort($results, function($a, $b) use ($searchTerms) {
            // First sort by importance (from Nominatim)
            if ($a['importance'] != $b['importance']) {
                return $b['importance'] <=> $a['importance'];
            }
            
            // Then check if name contains search terms
            $aName = strtolower($a['display_name']);
            $bName = strtolower($b['display_name']);
            
            $aMatches = 0;
            $bMatches = 0;
            
            foreach ($searchTerms as $term) {
                if (strlen($term) > 2) {
                    if (strpos($aName, $term) !== false) $aMatches++;
                    if (strpos($bName, $term) !== false) $bMatches++;
                }
            }
            
            return $bMatches <=> $aMatches;
        });
        
        return $results;
    }

    public function selectSearchResult($lat, $lon, $name)
    {
        $this->latitude = $lat;
        $this->longitude = $lon;
        $this->location_name = $name;
        $this->searchResults = [];
        $this->showResults = false;
        $this->searchLocation = $name;
        
        // Center map on selected location
        $this->dispatch('center-map', latitude: (float)$lat, longitude: (float)$lon, name: $name);
        
        $this->dispatch('notify', message: 'Location selected successfully!');
    }

    public function setLocation($latitude, $longitude, $name)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->location_name = $name;
    }

    public function save()
    {
        $this->validate();

        $imagePath = null;
        if ($this->image) {
            $imagePath = $this->image->store('event-images', 'public');
        }

        $event = Event::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date,
            'location_name' => $this->location_name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        session()->flash('message', 'Event created successfully!');

        return redirect()->route('events.show', $event);
    }

    public function render()
    {
        return view('livewire.create-event')->layout('layouts.app');
    }
}