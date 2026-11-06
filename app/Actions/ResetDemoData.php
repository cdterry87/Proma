<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\SendIssueNotifications;
use App\Jobs\SendProjectNotifications;
use App\Jobs\SendTaskNotifications;
use App\Models\Client;
use App\Models\NotificationLog;
use App\Models\User;
use App\Support\DemoDataset;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ResetDemoData
{
    private const string NAME = 'Demo User';

    private const string TITLE = 'Project Manager';

    public function __construct(
        private readonly DeleteClient $deleteClient,
        private readonly DemoDataset $dataset,
    ) {}

    /**
     * @throws InvalidArgumentException when the user is not a demo account.
     */
    public function handle(User $user): void
    {
        if (! $user->is_demo) {
            throw new InvalidArgumentException('Only demo accounts can be reset.');
        }

        DB::transaction(function () use ($user): void {
            $user->clients()->lazyById()->each(fn (Client $client) => $this->deleteClient->handle($client));
            $user->userNotifications()->delete();
            NotificationLog::query()->ownedBy($user)->delete();

            $this->resetProfile($user);
            $this->dataset->seed($user);
        });

        // Generated here so the account is complete before the nightly jobs run.
        SendIssueNotifications::dispatchSync($user);
        SendProjectNotifications::dispatchSync($user);
        SendTaskNotifications::dispatchSync($user);
    }

    private function resetProfile(User $user): void
    {
        $password = config('proma.demo.password');

        $user->update([
            'name' => self::NAME,
            'title' => self::TITLE,
            'email_notifications' => false,
            ...(filled($password) ? ['password' => $password] : []),
        ]);
    }
}
