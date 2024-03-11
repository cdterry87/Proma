<x-layouts.page title="Users">
    <x-slot:action-button>
        <x-modals.trigger
            id="users_form__modal"
            label="Add User"
            icon="plus"
            class="btn-primary btn-sm"
        />
    </x-slot:action-button>
    <livewire:users.users-table />
    <livewire:users.users-form />
    <livewire:users.users-permissions />
</x-layouts.page>
