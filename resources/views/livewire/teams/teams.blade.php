<x-layouts.page title="Teams">
    <x-slot:action-button>
        <x-modals.trigger
            id="teams_form__modal"
            label="Add Team"
            icon="plus"
            class="btn-primary btn-sm"
        />
    </x-slot:action-button>
    <livewire:teams.teams-table />
    <livewire:teams.teams-form />
    <livewire:teams.teams-users />
</x-layouts.page>
