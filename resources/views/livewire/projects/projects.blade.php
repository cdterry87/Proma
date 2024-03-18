<x-layouts.page title="Projects">
    <x-slot:action-button>
        <x-modals.trigger
            id="projects_form__modal"
            label="Add Project"
            icon="plus"
            class="btn-primary btn-sm"
        />
    </x-slot:action-button>
    <livewire:projects.projects-table />
    <livewire:projects.projects-form />
</x-layouts.page>
