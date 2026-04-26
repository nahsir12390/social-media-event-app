<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display the events feed page.
     */
    public function index()
    {
        return view('events.index');
    }

    /**
     * Display the create event page.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Display a specific event details page.
     */
    public function show(Event $event)
    {
        return view('events.show', ['event' => $event]);
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        // Check if user is the owner
        if ($event->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('events.edit', ['event' => $event]);
    }

    /**
     * Display the events map page.
     */
    public function map()
    {
        return view('events.map');
    }

    /**
     * Display user's events.
     */
    public function myEvents()
    {
        $createdEvents = Auth::user()->events()
            ->with(['likes', 'attendees'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'created');

        $attendingEvents = Auth::user()->attendingEvents()
            ->with(['user', 'likes', 'attendees'])
            ->where('event_date', '>=', now())
            ->orderBy('event_date', 'asc')
            ->paginate(10, ['*'], 'attending');

        return view('events.my-events', [
            'createdEvents' => $createdEvents,
            'attendingEvents' => $attendingEvents
        ]);
    }
}