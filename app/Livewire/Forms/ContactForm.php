<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\ClientContact;
use Livewire\Form;

class ContactForm extends Form
{
    public string $name = '';

    public string $title = '';

    public string $email = '';

    public string $phone = '';

    public string $phone_ext = '';

    public bool $active = true;

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'phone_ext' => ['nullable', 'string', 'max:5'],
            'active' => ['boolean'],
        ];
    }

    public function setContact(ClientContact $contact): void
    {
        $this->name = $contact->name;
        $this->title = $contact->title;
        $this->email = $contact->email;
        $this->phone = $contact->phone ?? '';
        $this->phone_ext = $contact->phone_ext ?? '';
        $this->active = $contact->active;
    }

    /**
     * @return array<string, mixed>
     */
    public function values(): array
    {
        $values = $this->validate();

        return [
            ...$values,
            'phone' => $values['phone'] ?: null,
            'phone_ext' => $values['phone_ext'] ?: null,
        ];
    }
}
