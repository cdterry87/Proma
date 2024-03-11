<x-layouts.page title="Issues">
    <x-slot:action-button>
        <x-modals.trigger
            id="issues_form__modal"
            label="Add Issue"
            icon="plus"
            class="btn-primary btn-sm"
        />
    </x-slot:action-button>
    <livewire:issues.issues-table />
    <livewire:issues.issues-form />
</x-layouts.page>
