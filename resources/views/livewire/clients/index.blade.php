<x-layouts.page title="Clients">
    <x-slot:action-button>
        <x-modals.trigger
            id="clients_form__modal"
            label="Add Client"
            icon="plus"
            class="btn-primary btn-sm"
        />
    </x-slot:action-button>
    <livewire:clients.table />
    <livewire:clients.form />
    <livewire:clients.contacts />
</x-layouts.page>
