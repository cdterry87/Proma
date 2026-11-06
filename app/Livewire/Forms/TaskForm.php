<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\Task;
use Livewire\Form;

class TaskForm extends Form
{
    public string $title = '';

    public string $description = '';

    public string $start_date = '';

    public string $due_date = '';

    public bool $completed = false;

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:65535'],
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
            'start_date' => 'start date',
            'due_date' => 'due date',
        ];
    }

    public function setTask(Task $task): void
    {
        $this->title = $task->title;
        $this->description = $task->description;
        $this->start_date = $task->start_date?->format('Y-m-d') ?? '';
        $this->due_date = $task->due_date?->format('Y-m-d') ?? '';
        $this->completed = $task->isCompleted();
    }

    /**
     * Issue tasks have no dates, whatever the request sends.
     *
     * @return array<string, mixed>
     */
    public function values(bool $withDates, ?Task $task = null): array
    {
        $values = $this->validate();

        return [
            'title' => $values['title'],
            'description' => $values['description'],
            'start_date' => $withDates ? ($values['start_date'] ?: null) : null,
            'due_date' => $withDates ? ($values['due_date'] ?: null) : null,
            'completed_date' => $values['completed'] ? ($task->completed_date ?? today()) : null,
        ];
    }
}
