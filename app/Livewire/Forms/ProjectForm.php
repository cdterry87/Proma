<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProjectForm extends Form
{
    public string $name = '';

    public string $description = '';

    public string $client_id = '';

    public string $start_date = '';

    public string $due_date = '';

    public bool $completed = false;

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')->where('user_id', Auth::id())],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', ...($this->start_date !== '' ? ['after_or_equal:start_date'] : [])],
            'completed' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'client_id' => 'client',
            'start_date' => 'start date',
            'due_date' => 'due date',
        ];
    }

    public function setProject(Project $project): void
    {
        $this->name = $project->name;
        $this->description = $project->description;
        $this->client_id = (string) $project->client_id;
        $this->start_date = $project->start_date?->format('Y-m-d') ?? '';
        $this->due_date = $project->due_date?->format('Y-m-d') ?? '';
        $this->completed = $project->isCompleted();
    }

    /**
     * @return array<string, mixed>
     */
    public function values(?Project $project = null): array
    {
        $values = $this->validate();

        return [
            'name' => $values['name'],
            'description' => $values['description'],
            'client_id' => (int) $values['client_id'],
            'start_date' => $values['start_date'] ?: null,
            'due_date' => $values['due_date'] ?: null,
            'completed_date' => $values['completed'] ? ($project->completed_date ?? today()) : null,
        ];
    }
}
