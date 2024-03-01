<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Table extends PowerGridComponent
{
    use WithExport;

    #[On('refreshTable')]
    public function datasource(): ?Collection
    {
        return Client::all();
    }

    public function setUp(): array
    {
        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
            Detail::make()
                ->view('livewire.clients.details')
                ->showCollapseIcon(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('contacts_count')
            ->add('notes_count')
            ->add('uploads_count')
            ->add('active', function ($entry) {
                return Client::getActiveCodes()->firstWhere('value', $entry->active)['label'];
            })
            ->add('created_at_formatted', function ($entry) {
                return Carbon::parse($entry->created_at)->format('m/d/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Contacts', 'contacts_count')
                ->sortable(),

            Column::make('Notes', 'notes_count')
                ->sortable(),

            Column::make('Uploads', 'uploads_count')
                ->sortable(),

            Column::make('Active', 'active')
                ->sortable(),

            Column::make('Created', 'created_at_formatted'),

            Column::action('Action')
        ];
    }

    public function actions(Client $row): array
    {
        return [
            // @todo - Add view button/link. Remove labels and use icons only.
            Button::add('client-form--button')
                ->slot('<x-modals.trigger
                    id="clients_form__modal"
                    label="Edit"
                    label-classes="hidden sm:block"
                    icon="edit"
                    class="btn-secondary btn-sm"
                    title="Edit Client"
                />')
                ->dispatchTo('clients.form', 'edit', ['id' => $row->id]),
            Button::add('client-contacts--button')
                ->slot('<x-modals.trigger
                    id="clients_contacts__modal"
                    label="Contacts"
                    label-classes="hidden sm:block"
                    icon="phone"
                    class="btn-accent btn-sm"
                    title="Edit Contacts"
                />')
                ->dispatchTo('clients.contacts', 'edit', ['id' => $row->id]),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('active', 'active')
                ->dataSource(Client::getActiveCodes())
                ->optionValue('value')
                ->optionLabel('label'),
        ];
    }
}
