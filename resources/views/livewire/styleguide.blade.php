<div class="page">
    <x-page-header title="Style Reference">
        <x-slot:actions>
            <x-badge variant="info">Local only</x-badge>
        </x-slot:actions>
    </x-page-header>

    <x-card title="Color" subtitle="Semantic tokens for the current theme">
        <div class="swatches">
            @foreach (['bg', 'surface', 'sunken', 'primary', 'info', 'success', 'warning', 'error'] as $token)
                <div class="swatch swatch--{{ $token }}">{{ $token }}</div>
            @endforeach
        </div>
    </x-card>

    <x-card title="Typography">
        <div class="stack">
            <p class="logo sidebar__logo">{{ config('app.name') }}</p>
            <h1>Heading one</h1>
            <h2>Heading two</h2>
            <h3>Heading three</h3>
            <p>Body text with <a href="#main">a link</a>.</p>
            <p class="muted">Muted supporting text.</p>
        </div>
    </x-card>

    <x-card title="Buttons">
        <div class="cluster">
            <x-button variant="primary" icon="plus">Primary</x-button>
            <x-button>Default</x-button>
            <x-button>Neutral</x-button>
            <x-button variant="success" icon="success">Success</x-button>
            <x-button variant="danger" icon="delete">Danger</x-button>
            <x-button variant="ghost">Ghost</x-button>
            <x-button variant="primary" disabled>Disabled</x-button>
        </div>
        <div class="cluster">
            <x-button variant="primary" size="sm" icon="edit">Small</x-button>
            <x-button variant="ghost" size="sm" icon="eye" label="View" />
            <x-button variant="danger" size="sm" icon="delete" label="Delete" />
            <x-button variant="ghost" icon="search" label="Search" />
            <x-button variant="primary" href="#main">Link button</x-button>
        </div>
    </x-card>

    <x-card title="Badges and alerts">
        <div class="cluster">
            <x-badge>Default</x-badge>
            <x-badge variant="info">Medium</x-badge>
            <x-badge variant="success">Low</x-badge>
            <x-badge variant="warning">High</x-badge>
            <x-badge variant="error">Critical</x-badge>
        </div>
        <x-alert variant="info">An informational message.</x-alert>
        <x-alert variant="success">A success message.</x-alert>
        <x-alert variant="warning">A warning message.</x-alert>
        <x-alert variant="error">An error message.</x-alert>
    </x-card>

    <div class="grid-auto">
        <x-stat label="Projects Created" value="12" hint="+20% Increase" icon="projects" />
        <x-stat label="Projects Completed" value="7" hint="No prior data" icon="success" />
        <x-stat label="Issues Opened" value="31" hint="-5% Decrease" icon="issues" />
        <x-stat label="Issues Resolved" value="28" hint="0% Change" icon="success" />
    </div>

    <x-card title="Form controls">
        <div class="form">
            <div class="form__row">
                <x-input name="sample_text" label="Text" hint="Hint text sits below the control." placeholder="Placeholder" />
                <x-input name="sample_date" label="Date" type="date" />
            </div>
            <x-select name="sample_select" label="Select">
                <option value="">Choose an option</option>
                <option value="1">Option one</option>
            </x-select>
            <x-textarea name="sample_textarea" label="Textarea" />
            <div class="cluster">
                <x-checkbox name="sample_checkbox" label="Remember me" />
                <x-toggle name="sample_toggle" label="Active" checked />
            </div>
        </div>
    </x-card>

    <x-card title="Dialogs and toasts" subtitle="Submit the form empty to see validation inside the dialog">
        <div class="cluster">
            <x-button variant="primary" dialog="sample-form">Open form dialog</x-button>
            <x-button variant="danger" dialog="sample-delete">Open confirm dialog</x-button>
        </div>
        <div class="cluster">
            @foreach (['info', 'success', 'warning', 'error'] as $type)
                <x-button size="sm" wire:click="toast('{{ $type }}', 'A sample {{ $type }} toast.')">Toast {{ $type }}</x-button>
            @endforeach
        </div>
    </x-card>

    <div class="stack">
        <h2>Table</h2>
        <x-table caption="Sample records">
            <thead>
                <tr>
                    <x-table.head sortable="name" :sort="$sort" :direction="$direction">Name</x-table.head>
                    <x-table.head sortable="status" :sort="$sort" :direction="$direction">Status</x-table.head>
                    <x-table.head><span class="visually-hidden">Actions</span></x-table.head>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <x-table.row wire:key="row-{{ $row['id'] }}">
                        <x-table.cell label="Name">{{ $row['name'] }}</x-table.cell>
                        <x-table.cell label="Status">
                            <x-badge :variant="$row['status'] === 'Open' ? 'warning' : 'success'">{{ $row['status'] }}</x-badge>
                        </x-table.cell>
                        <x-table.cell>
                            <div class="table__actions">
                                <x-button variant="ghost" size="sm" icon="eye" label="View" />
                                <x-button variant="danger-ghost" size="sm" icon="delete" label="Delete" dialog="sample-delete" />
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @endforeach
            </tbody>
        </x-table>
        <x-pagination :paginator="$rows" />
    </div>

    <x-card title="Timestamps">
        <x-timestamps :created="now()->subDays(3)" :updated="now()" />
    </x-card>

    <x-dialog name="sample-form" title="Sample form" subtitle="Shared dialog with validation">
        <form class="form" wire:submit="save">
            <x-input name="name" id="dialog-name" label="Name" wire:model="name" required />
            <x-input name="email" id="dialog-email" label="Email" type="email" wire:model="email" required />
            <x-select name="priority" id="dialog-priority" label="Priority" wire:model="priority" required>
                <option value="">Select priority</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
                <option value="4">Critical</option>
            </x-select>
            <x-textarea name="description" id="dialog-description" label="Description" wire:model="description" required />
            <x-toggle name="active" id="dialog-active" label="Active" wire:model="active" />
            <div class="form__actions">
                <x-button variant="ghost" data-close-dialog>Cancel</x-button>
                <x-button variant="primary" type="submit">Save</x-button>
            </div>
        </form>
    </x-dialog>

    <x-confirm-dialog name="sample-delete" title="Delete sample" action="confirmDelete">
        This deletes the record and all of its attached records.
    </x-confirm-dialog>
</div>
