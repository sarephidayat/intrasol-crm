<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdminUser extends Command
{
    protected $signature = 'admin:create {--email=} {--name=}';

    protected $description = 'Create the single administrator account (interactive, does not log password)';

    public function handle(): int
    {
        $email = $this->option('email') ?? $this->ask('Admin email');
        $name = $this->option('name') ?? $this->ask('Admin name');

        if (! $email || ! $name) {
            $this->error('Email and name are required.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');
            return self::FAILURE;
        }

        if (User::count() >= 1) {
            $this->warn('There is already at least one user. This command is intended for provisioning the single administrator.');
            if (! $this->confirm('Continue anyway?')) {
                return self::FAILURE;
            }
        }

        $password = $this->secret('Admin password');
        $passwordConfirm = $this->secret('Confirm admin password');

        if ($password !== $passwordConfirm) {
            $this->error('Passwords do not match.');
            return self::FAILURE;
        }

        $validator = Validator::make(['email' => $email, 'password' => $password], [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', Password::defaults()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->info('Administrator account created successfully.');
        return self::SUCCESS;
    }
}
