<form class="form" wire:submit="save">
    <x-input name="form.name" id="client-name" label="Name" wire:model="form.name" required />
    <x-textarea name="form.description" id="client-description" label="Description" wire:model="form.description" required />
    <x-toggle name="form.active" id="client-active" label="Active" wire:model="form.active" />
    <div class="form__actions">
        <x-button variant="ghost" data-close-dialog>Cancel</x-button>
        <x-button variant="primary" type="submit">Save</x-button>
    </div>
</form>
