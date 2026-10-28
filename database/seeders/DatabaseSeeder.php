<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('demo.admin_email');
        if (! User::where('email', $email)->exists() && ! config('demo.admin_password')) {
            throw new RuntimeException('Set ADMIN_PASSWORD before creating the admin account.');
        }
        if (config('demo.enabled') && ! config('demo.password')) {
            throw new RuntimeException('Set DEMO_PASSWORD when SEED_DEMO_DATA is enabled.');
        }

        DB::transaction(function () use ($email) {
            User::firstOrCreate(['email' => $email], [
                'name' => 'Campus Admin',
                'password' => config('demo.admin_password'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'bio' => 'CampusConnect community administrator.',
            ]);

            if (config('demo.enabled')) {
                $this->call(DemoSeeder::class);
            }
        });
    }
}
