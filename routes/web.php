<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Livewire\EventFeed;
use App\Livewire\CreateEvent;
use App\Livewire\EventDetails;
use App\Livewire\EditEvent;
Route::view('/', 'welcome');

// Event routes - protected by auth middleware
Route::middleware(['auth', 'verified'])->group(function () {
    // Event Feed (Livewire)
    Route::get('/events', EventFeed::class)->name('events.feed');
    
    // Create Event (Livewire)
    Route::get('/events/create', CreateEvent::class)->name('events.create');
    
    // Event Details (Livewire)
    Route::get('/events/{event}', EventDetails::class)->name('events.show');
     Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit'); 
    
    // Map View (Controller)
    Route::get('/map', [EventController::class, 'map'])->name('events.map');
    
    // My Events (Controller)
    Route::get('/my-events', [EventController::class, 'myEvents'])->name('my-events');
    
    // Public Profile
    Route::get('/profile/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile.edit');

require __DIR__.'/auth.php';