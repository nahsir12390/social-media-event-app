<?php

namespace Database\Seeders;

use App\Models\Attendee;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Ada Okafor', 'Tunde Adeyemi', 'Zainab Bello', 'Chidi Eze', 'Fatima Yusuf', 'David Johnson', 'Amaka Obi', 'Samuel Adebayo', 'Aisha Musa', 'Emeka Nwosu', 'Grace Williams', 'Ibrahim Hassan'];
        $users = collect($names)->map(fn ($name, $index) => User::firstOrCreate(
            ['email' => $index === 0 ? 'demo@campusconnect.test' : 'member'.($index + 1).'@campusconnect.test'],
            ['name' => $name, 'password' => config('demo.password'), 'email_verified_at' => now(), 'bio' => 'Student, community member, and campus event enthusiast.'],
        ));

        $events = [
            ['Campus Tech Meetup', 'Build something together! Bring your laptop for demos, coding sessions, and conversations with student developers.', 'Innovation Hub, University of Lagos', 6.5155, 3.3895, 1],
            ['Afrobeats on the Lawn', 'An evening of live music, student performers, and good company. Bring a friend and a picnic blanket.', 'Senate Garden, University of Lagos', 6.5190, 3.3970, 2],
            ['Five-a-side Football', 'Friendly matches for all abilities. Sign up as an individual or bring your team. Water breaks and refreshments provided.', 'Sports Centre, University of Lagos', 6.5120, 3.3940, 3],
            ['Design and Coffee', 'Share your portfolio, explore design ideas, and get friendly feedback over coffee. Beginners welcome.', 'Creative Studio, Yaba', 6.5090, 3.3760, 4],
            ['Career Launch Workshop', 'Practice interviews, improve your CV, and meet alumni working in technology, business, and the creative industries.', 'Main Auditorium, University of Lagos', 6.5200, 3.3990, 5],
            ['Campus Food Fair', 'Discover student chefs and local favourites. Come hungry for jollof, small chops, and fresh fruit drinks.', 'Student Union Courtyard', 6.5170, 3.3910, 6],
            ['Photography Walk', 'Explore the lagoon and campus gardens with fellow photographers. Phone cameras are welcome.', 'Lagoon Front, University of Lagos', 6.5240, 3.4020, 8],
            ['Open Mic Night', 'Poetry, acoustic music, comedy, and spoken word. Reserve a slot or join the audience to cheer on your friends.', 'Arts Theatre, University of Lagos', 6.5180, 3.3950, 10],
            ['Community Clean-up', 'Help make our neighbourhood cleaner. Gloves and bags provided, followed by a relaxed community lunch.', 'Yaba Community Centre', 6.5060, 3.3710, 14],
            ['Founders and Ideas', 'Pitch an idea, find a collaborator, or learn how student founders got started. All disciplines are welcome.', 'Enterprise Centre, University of Lagos', 6.5210, 3.3920, 21],
            ['Last Week Board Games', 'A relaxed afternoon of chess, Scrabble, and new friendships. Thanks to everyone who joined!', 'Student Union Lounge', 6.5160, 3.3930, -3],
        ];
        foreach ($events as $index => [$title, $description, $location, $lat, $lng, $days]) {
            // Existing events and interactions belong to the running demo; never reset them.
            $event = Event::firstOrCreate(['title' => $title, 'user_id' => $users[$index % $users->count()]->id], [
                'description' => $description, 'location_name' => $location,
                'latitude' => $lat, 'longitude' => $lng,
                'event_date' => now()->addDays($days)->setTime(17, 0),
                'created_at' => now()->subHours(24 - $index), 'is_active' => true,
            ]);
            if (! $event->wasRecentlyCreated) {
                continue;
            }
            foreach ($users as $member => $user) {
                if (($member + $index) % 3 !== 0) {
                    Like::create(['event_id' => $event->id, 'user_id' => $user->id]);
                }
                Attendee::create(['event_id' => $event->id, 'user_id' => $user->id, 'status' => ($member + $index) % 3 === 0 ? 'interested' : 'attending']);
                if ($member < 4) {
                    Comment::create(['event_id' => $event->id, 'user_id' => $user->id, 'comment' => [
                        'Looking forward to meeting everyone there!',
                        'Can I bring a friend from another department?',
                        'Absolutely, everyone is welcome. See you there!',
                        'Added this to my plans. Thanks for organising!',
                    ][$member], 'created_at' => now()->subMinutes(180 - $member * 30)]);
                }
            }
        }
    }
}
