<x-layouts.page title="Clients">
    <x-slot:action-button>
        <x-modals.trigger
            id="clients_form__modal"
            label="Add Client"
            icon="plus"
            class="btn-primary btn-sm"
        />
    </x-slot:action-button>

    <livewire:clients.clients-table />
    <livewire:clients.clients-form />
</x-layouts.page>
