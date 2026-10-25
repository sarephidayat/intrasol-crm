<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class InitialUserSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('INITIAL_ADMIN_NAME');
        $email = env('INITIAL_ADMIN_EMAIL');
        $password = env('INITIAL_ADMIN_PASSWORD');

        if (! $name || ! $email || ! $password) {
            $this->command->error('INITIAL_ADMIN_NAME, INITIAL_ADMIN_EMAIL, INITIAL_ADMIN_PASSWORD must be set in .env');
            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->command->warn('User with this email already exists, skipping.');
            return;
        }

        if (User::count() >= 1) {
            $this->command->warn('Users already exist, skipping to avoid overwriting.');
            return;
        }

        $validator = Validator::make(['email' => $email, 'password' => $password], [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->command->error($error);
            }
            return;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Initial admin created.');
    }
}
