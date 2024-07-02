<?php

namespace App\Livewire\Clients;

use Livewire\Attributes\On;
use App\Models\ClientContact;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ClientsContactsTable extends PowerGridComponent
{
    public $clientId;

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
        ];
    }

    #[On('refreshData')]
    public function datasource(): Builder
    {
        return ClientContact::query()
            ->where('client_id', $this->clientId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('title')
            ->add('email')
            ->add('phone')
            ->add('phone_ext')
            ->add('active', function ($entry) {
                return ClientContact::getActiveCodes()->firstWhere('value', $entry->active)['label'];
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Title', 'title')
                ->searchable()
                ->sortable(),

            Column::make('Email', 'email')
                ->searchable()
                ->sortable(),

            Column::make('Phone', 'phone')
                ->searchable(),

            Column::make('Ext.', 'phone_ext'),

            Column::make('Active', 'active')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function actions(ClientContact $row): array
    {
        return [
            Button::add('client-contacts--button')
                ->slot('<x-modals.trigger
                    id="clients_contacts_form__modal"
                    icon="edit"
                    class="btn-secondary btn-sm"
                    title="Edit Contact"
                />')
                ->dispatchTo('clients.clients-contacts-form', 'editContact', ['id' => $row->id]),
            Button::add('client-contacts-delete--button')
                ->slot('<x-icons.delete />')
                ->class('btn btn-sm btn-error')
                ->tooltip('Delete Contact')
                ->dispatchTo('clients.clients-contacts-form', 'deleteContact', ['contactId' => $row->id, 'clientId' => $row->client_id]),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('active', 'active')
                ->dataSource(ClientContact::getActiveCodes())
                ->optionValue('value')
                ->optionLabel('label'),
        ];
    }
}
