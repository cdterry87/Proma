<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Client;
use Livewire\Form;

class ClientForm extends Form
{
    public string $name = '';

    public string $description = '';

    public bool $active = true;

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
            'active' => ['boolean'],
        ];
    }

    public function setClient(Client $client): void
    {
        $this->name = $client->name;
        $this->description = $client->description;
        $this->active = $client->active;
    }
}
