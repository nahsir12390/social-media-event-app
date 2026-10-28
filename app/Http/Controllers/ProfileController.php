<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(User $user): View
    {
        return view('profiles.show', [
            'user' => $user,
            'events' => $user->events()->where('is_active', true)->latest('event_date')->paginate(9),
        ]);
    }
}
