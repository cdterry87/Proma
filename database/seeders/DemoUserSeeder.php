<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Actions\ResetDemoData;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoUserSeeder extends Seeder
{
    /**
     * Creates the demo account from configuration and fills it. A real account
     * with the same email is left alone, because a reset deletes all of its data.
     */
    public function run(ResetDemoData $reset): void
    {
        $email = config('proma.demo.email');

        if (blank($email) || blank(config('proma.demo.password'))) {
            $this->command?->warn('Skipped the demo user: set PROMA_DEMO_EMAIL and PROMA_DEMO_PASSWORD.');

            return;
        }

        $user = User::query()->where('email', User::normalizeEmail($email))->first();

        if ($user !== null && ! $user->is_demo) {
            $this->command?->error("Skipped the demo user: {$email} belongs to a real account.");

            return;
        }

        if ($user === null) {
            $user = new User([
                'name' => 'Demo User',
                'email' => $email,
                'password' => Str::random(40),
            ]);
            $user->forceFill(['is_demo' => true])->save();
        }

        $reset->handle($user);
    }
}
