<section class="stack" aria-labelledby="contacts-heading">
    <h2 id="contacts-heading">Contacts</h2>

    <x-table.toolbar placeholder="Search contacts" :filters="$tableFilters" id-prefix="contacts">
        <x-slot:actions>
            <x-button variant="primary" icon="plus" wire:click="create">Add contact</x-button>
        </x-slot:actions>
    </x-table.toolbar>

    <x-table caption="Contacts">
        <thead>
            <tr>
                <x-table.head sortable="name" :sort="$activeSort" :direction="$activeDirection">Name</x-table.head>
                <x-table.head sortable="title" :sort="$activeSort" :direction="$activeDirection">Title</x-table.head>
                <x-table.head sortable="email" :sort="$activeSort" :direction="$activeDirection">Email</x-table.head>
                <x-table.head sortable="phone" :sort="$activeSort" :direction="$activeDirection">Phone</x-table.head>
                <x-table.head>Ext.</x-table.head>
                <x-table.head sortable="active" :sort="$activeSort" :direction="$activeDirection">Status</x-table.head>
                <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $contact)
                <x-table.row wire:key="contact-{{ $contact->id }}">
                    <x-table.cell label="Name">{{ $contact->name }}</x-table.cell>
                    <x-table.cell label="Title">{{ $contact->title }}</x-table.cell>
                    <x-table.cell label="Email"><a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></x-table.cell>
                    <x-table.cell label="Phone" class="table__cell--nowrap">{{ $contact->phone }}</x-table.cell>
                    <x-table.cell label="Ext.">{{ $contact->phone_ext }}</x-table.cell>
                    <x-table.cell label="Status">
                        <x-badge :variant="$contact->active ? 'success' : null">{{ $contact->active ? 'Active' : 'Inactive' }}</x-badge>
                    </x-table.cell>
                    <x-table.cell>
                        <div class="table__actions">
                            <x-button variant="ghost" size="sm" icon="edit" :label="'Edit '.$contact->name" wire:click="edit({{ $contact->id }})" />
                            <x-button variant="danger-ghost" size="sm" icon="delete" :label="'Delete '.$contact->name" wire:click="confirmDelete({{ $contact->id }})" />
                        </div>
                    </x-table.cell>
                </x-table.row>
            @empty
                <x-table.empty colspan="7">No contacts found.</x-table.empty>
            @endforelse
        </tbody>
    </x-table>

    <x-table.footer :rows="$rows" :per-page-options="$perPageOptions" id-prefix="contacts" />

    <x-dialog name="contact-form" :title="$editingId ? 'Edit contact' : 'Add contact'">
        <form class="form" wire:submit="save">
            <x-input name="form.name" id="contact-name" label="Name" wire:model="form.name" required />
            <x-input name="form.title" id="contact-title" label="Title" wire:model="form.title" required />
            <x-input name="form.email" id="contact-email" label="Email" type="email" wire:model="form.email" required />
            <div class="form__row">
                <x-input name="form.phone" id="contact-phone" label="Phone" type="tel" wire:model="form.phone" />
                <x-input name="form.phone_ext" id="contact-phone-ext" label="Ext." wire:model="form.phone_ext" />
            </div>
            <x-toggle name="form.active" id="contact-active" label="Active" wire:model="form.active" />
            <div class="form__actions">
                <x-button variant="ghost" data-close-dialog>Cancel</x-button>
                <x-button variant="primary" type="submit">Save</x-button>
            </div>
        </form>
    </x-dialog>

    <x-confirm-dialog name="contact-delete" title="Delete contact" action="delete">
        This permanently deletes {{ $deletingName }}.
    </x-confirm-dialog>
</section>
