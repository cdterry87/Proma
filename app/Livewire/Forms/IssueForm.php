<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Enums\IssuePriority;
use App\Models\Issue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Form;

class IssueForm extends Form
{
    public string $name = '';

    public string $description = '';

    public string $priority = '';

    public string $client_id = '';

    public string $project_id = '';

    public bool $resolved = false;

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
            'priority' => ['required', Rule::enum(IssuePriority::class)],
            'client_id' => ['required', 'integer', Rule::exists('clients', 'id')->where('user_id', Auth::id())],
            'project_id' => [
                'nullable',
                'integer',
                Rule::exists('projects', 'id')
                    ->where('user_id', Auth::id())
                    ->where('client_id', (int) $this->client_id),
            ],
            'resolved' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function validationAttributes(): array
    {
        return [
            'client_id' => 'client',
            'project_id' => 'project',
        ];
    }

    public function setIssue(Issue $issue): void
    {
        $this->name = $issue->name;
        $this->description = $issue->description;
        $this->priority = (string) $issue->priority->value;
        $this->client_id = (string) $issue->client_id;
        $this->project_id = (string) $issue->project_id;
        $this->resolved = $issue->isResolved();
    }

    /**
     * @return array<string, mixed>
     */
    public function values(?Issue $issue = null): array
    {
        $values = $this->validate();

        return [
            'name' => $values['name'],
            'description' => $values['description'],
            'priority' => (int) $values['priority'],
            'client_id' => (int) $values['client_id'],
            'project_id' => $values['project_id'] ? (int) $values['project_id'] : null,
            'resolved_date' => $values['resolved'] ? ($issue->resolved_date ?? today()) : null,
        ];
    }
}
