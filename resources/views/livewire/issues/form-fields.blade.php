@use('App\Enums\IssuePriority')

<form class="form" wire:submit="save">
    <x-input name="form.name" id="issue-name" label="Name" wire:model="form.name" required />
    <x-textarea name="form.description" id="issue-description" label="Description" wire:model="form.description" required />
    <x-select name="form.priority" id="issue-priority" label="Priority" wire:model="form.priority" required>
        <option value="">Select a priority</option>
        @foreach (IssuePriority::cases() as $priority)
            <option value="{{ $priority->value }}">{{ $priority->label() }}</option>
        @endforeach
    </x-select>
    <x-select name="form.client_id" id="issue-client" label="Client" wire:model.live="form.client_id" required>
        <option value="">Select a client</option>
        @foreach ($clientOptions as $id => $name)
            <option value="{{ $id }}" wire:key="client-option-{{ $id }}">{{ $name }}</option>
        @endforeach
    </x-select>
    @if ($form->client_id !== '')
        <x-select name="form.project_id" id="issue-project" label="Project" hint="Optional. Lists this client's incomplete projects." wire:model="form.project_id">
            <option value="">No project</option>
            @foreach ($projectOptions as $id => $name)
                <option value="{{ $id }}" wire:key="project-option-{{ $id }}">{{ $name }}</option>
            @endforeach
        </x-select>
    @endif
    @isset($issue)
        <x-toggle name="form.resolved" id="issue-resolved" label="Resolved" wire:model="form.resolved" />
    @endisset
    <div class="form__actions">
        <x-button variant="ghost" data-close-dialog>Cancel</x-button>
        <x-button variant="primary" type="submit">Save</x-button>
    </div>
</form>
