<?php

use App\Livewire\EventFeed;
use App\Models\Attendee;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Like;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('seeds working demo accounts and activity without resetting changes', function () {
    config(['demo.enabled' => true, 'demo.admin_password' => 'Admin-Example-123!', 'demo.password' => 'Demo-Example-123!']);
    $this->seed();
    $admin = User::where('email', 'admin@campusconnect.test')->firstOrFail();
    expect($admin->is_admin)->toBeTrue();
    expect(Hash::check('Admin-Example-123!', $admin->password))->toBeTrue();
    expect(Event::count())->toBe(11);
    expect(Like::count())->toBeGreaterThan(0);
    expect(Comment::count())->toBe(44);
    expect(Attendee::count())->toBe(132);
    $demo = User::where('email', 'demo@campusconnect.test')->firstOrFail();
    Livewire::actingAs($demo)->test(EventFeed::class)->assertStatus(200)
        ->set('userLatitude', 6.5155)->set('userLongitude', 3.3895)
        ->set('filter', 'nearby')->assertSee('Campus Tech Meetup');
    $this->actingAs($demo)->get(route('profile.show', $demo))->assertOk()->assertSee($demo->name);
    $admin->update(['password' => 'Changed-Password-123!']);
    Like::first()->delete();
    $this->seed();
    expect(User::count())->toBe(13);
    expect(Event::count())->toBe(11);
    expect(Comment::count())->toBe(44);
    expect(Like::count())->toBe(87);
    expect(Hash::check('Changed-Password-123!', $admin->fresh()->password))->toBeTrue();
});
