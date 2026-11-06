<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    /**
     * @var string
     */
    protected $signature = 'proma:user:create';

    /**
     * @var string
     */
    protected $description = 'Create a user with a prompted password';

    public function handle(): int
    {
        $name = text(
            label: 'Name',
            validate: fn (string $value): ?string => $this->firstError($value, ['required', 'string', 'max:255']),
        );

        $email = text(
            label: 'Email',
            validate: fn (string $value): ?string => $this->firstError(
                User::normalizeEmail($value),
                ['required', 'email', 'max:255', 'unique:users,email'],
            ),
        );

        $password = password(
            label: 'Password',
            validate: fn (string $value): ?string => $this->firstError($value, ['required', Password::defaults()]),
        );

        User::query()->create(compact('name', 'email', 'password'));

        $this->info("Created {$email}.");

        return self::SUCCESS;
    }

    /**
     * @param  array<int, mixed>  $rules
     */
    private function firstError(string $value, array $rules): ?string
    {
        return Validator::make(['value' => $value], ['value' => $rules])->errors()->first('value') ?: null;
    }
}
