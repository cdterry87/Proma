<form class="form" wire:submit="save">
    <x-input name="form.name" id="project-name" label="Name" wire:model="form.name" required />
    <x-select name="form.client_id" id="project-client" label="Client" wire:model="form.client_id" required>
        <option value="">Select a client</option>
        @foreach ($clientOptions as $id => $name)
            <option value="{{ $id }}" wire:key="client-option-{{ $id }}">{{ $name }}</option>
        @endforeach
    </x-select>
    <x-textarea name="form.description" id="project-description" label="Description" wire:model="form.description" required />
    <div class="form__row">
        <x-input name="form.start_date" id="project-start" label="Start date" type="date" wire:model="form.start_date" />
        <x-input name="form.due_date" id="project-due" label="Due date" type="date" wire:model="form.due_date" />
    </div>
    @isset($project)
        <x-toggle name="form.completed" id="project-completed" label="Completed" wire:model="form.completed" />
    @endisset
    <div class="form__actions">
        <x-button variant="ghost" data-close-dialog>Cancel</x-button>
        <x-button variant="primary" type="submit">Save</x-button>
    </div>
</form>
