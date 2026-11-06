@use('App\Support\DateFormatter')

@php
    $columns = $withDates ? 6 : 4;
@endphp

<section class="stack" aria-labelledby="tasks-heading">
    <h2 id="tasks-heading">Tasks</h2>

    <x-table.toolbar placeholder="Search tasks" :filters="$tableFilters" id-prefix="tasks">
        <x-slot:actions>
            <x-button variant="primary" icon="plus" wire:click="create">Add task</x-button>
        </x-slot:actions>
    </x-table.toolbar>

    <x-table caption="Tasks">
        <thead>
            <tr>
                <x-table.head><span class="visually-hidden">Details</span></x-table.head>
                <x-table.head sortable="title" :sort="$activeSort" :direction="$activeDirection">Title</x-table.head>
                @if ($withDates)
                    <x-table.head sortable="start_date" :sort="$activeSort" :direction="$activeDirection">Start</x-table.head>
                    <x-table.head sortable="due_date" :sort="$activeSort" :direction="$activeDirection">Due</x-table.head>
                @endif
                <x-table.head sortable="completed_date" :sort="$activeSort" :direction="$activeDirection">Completed</x-table.head>
                <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
            </tr>
        </thead>
        @forelse ($rows as $task)
            <x-table.expandable-row :colspan="$columns" :label="'Show description of '.$task->title" :detail="$task->description" wire:key="task-{{ $task->uuid }}">
                <x-table.cell label="Title">{{ $task->title }}</x-table.cell>
                @if ($withDates)
                    <x-table.cell label="Start" class="table__cell--nowrap">{{ DateFormatter::format($task->start_date, 'Not started') }}</x-table.cell>
                    <x-table.cell label="Due" class="table__cell--nowrap">
                        @if ($task->isOverdue())
                            <x-badge variant="error">{{ DateFormatter::format($task->due_date) }}</x-badge>
                        @else
                            {{ DateFormatter::format($task->due_date, 'Not due') }}
                        @endif
                    </x-table.cell>
                @endif
                <x-table.cell label="Completed" class="table__cell--nowrap">{{ DateFormatter::format($task->completed_date, 'Incomplete') }}</x-table.cell>
                <x-table.cell>
                    <div class="table__actions">
                        <x-button variant="ghost" size="sm" icon="edit" :label="'Edit '.$task->title" :wire:click="'edit(\''.$task->uuid.'\')'" />
                        <x-button variant="danger-ghost" size="sm" icon="delete" :label="'Delete '.$task->title" :wire:click="'confirmDelete(\''.$task->uuid.'\')'" />
                    </div>
                </x-table.cell>
            </x-table.expandable-row>
        @empty
            <tbody>
                <x-table.empty :colspan="$columns">No tasks found.</x-table.empty>
            </tbody>
        @endforelse
    </x-table>

    <x-table.footer :rows="$rows" :per-page-options="$perPageOptions" id-prefix="tasks" />

    <x-dialog name="task-form" :title="$editingUuid ? 'Edit task' : 'Add task'">
        <form class="form" wire:submit="save">
            <x-input name="form.title" id="task-title" label="Title" wire:model="form.title" required />
            <x-textarea name="form.description" id="task-description" label="Description" wire:model="form.description" required />
            @if ($withDates)
                <div class="form__row">
                    <x-input name="form.start_date" id="task-start" label="Start date" type="date" wire:model="form.start_date" />
                    <x-input name="form.due_date" id="task-due" label="Due date" type="date" wire:model="form.due_date" />
                </div>
            @endif
            @if ($editingUuid)
                <x-toggle name="form.completed" id="task-completed" label="Completed" wire:model="form.completed" />
            @endif
            <div class="form__actions">
                <x-button variant="ghost" data-close-dialog>Cancel</x-button>
                <x-button variant="primary" type="submit">Save</x-button>
            </div>
        </form>
    </x-dialog>

    <x-confirm-dialog name="task-delete" title="Delete task" action="delete">
        This permanently deletes {{ $deletingTitle }}.
    </x-confirm-dialog>
</section>
