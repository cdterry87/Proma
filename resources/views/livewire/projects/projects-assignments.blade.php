<x-layouts.card title="Assignments">
    <x-slot:top-actions>
        <x-modals.trigger
            id="projects_assignments_form__modal"
            label="Add"
            icon="plus"
            class="btn-accent btn-sm"
            wire:click="$dispatchTo('projects.projects-assignments-form', 'getProject', { id: {{ $projectId }}})"
        />
    </x-slot:top-actions>

    @if ($assignments->isNotEmpty())
        <ul class="menu bg-base-300 dark:bg-base-200 w-full">
            @foreach ($assignments as $assignment)
                <li>
                    <a class="flex items-center justify-between gap-2">
                        <span>
                            {{ $assignment->assignedTo->name }}
                        </span>
                        <button
                            class="btn btn-sm btn-error"
                            wire:click.prevent="$dispatchTo('projects.projects-assignments-form', 'deleteAssignment', { assignmentId: {{ $assignment->id }}, projectId: {{ $projectId }}})"
                        >
                            <x-icons.delete />
                        </button>
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <p>
            No assignments found.
        </p>
    @endif
</x-layouts.card>
