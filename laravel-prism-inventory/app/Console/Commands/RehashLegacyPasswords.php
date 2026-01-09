<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class RehashLegacyPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * You will run it as: php artisan app:rehash-legacy-passwords
     */
    protected $signature = 'app:rehash-legacy-passwords';

    /**
     * The console command description.
     */
    protected $description = 'Re-hash legacy plain-text (or non-bcrypt) passwords using bcrypt for all users.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning users for legacy (non-bcrypt) passwords...');

        $count = 0;

        /** @var \App\Models\User $user */
        foreach (User::all() as $user) {
            $password = (string) $user->password;

            // If it already looks like a bcrypt hash ("$2y$" prefix), skip it
            if (str_starts_with($password, '$2y$')) {
                continue;
            }

            // For now, we will assume current stored password is the REAL plain-text
            // or a legacy hash that we want to replace with a new bcrypt hash.
            // If you know the actual plain password (e.g. "admin123"), you can hard-code it here per user.

            // IMPORTANT: this will change the password so that the current DB value becomes
            // the password text people must use to log in, but now stored securely as bcrypt.
            $this->line("Rehashing password for user ID {$user->uid} ({$user->username})...");

            $user->password = Hash::make($password);
            $user->save();

            $count++;
        }

        $this->info("Done. Rehashed {$count} user(s).");

        return Command::SUCCESS;
    }
}