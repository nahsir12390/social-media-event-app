<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'event_date',
        'location_name',
        'latitude',
        'longitude',
        'image',
        'is_active'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class);
    }

    // Check if user has liked this event
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // Check if user is attending this event
    public function isAttendedBy(User $user): bool
    {
        return $this->attendees()
            ->where('user_id', $user->id)
            ->where('status', 'attending')
            ->exists();
    }

    // Scope for nearby events using Haversine formula
    public function scopeNearby($query, $latitude, $longitude, $radius = 5)
    {
        return $query->selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        )->having('distance', '<=', $radius)
         ->orderBy('distance');
    }

    // Scope for upcoming events
    public function scopeUpcoming($query)
    {
        return $query->where('event_date', '>=', now())
                     ->orderBy('event_date', 'asc');
    }
}
