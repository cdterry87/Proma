<?php

namespace App\Livewire\Clients;

use App\Models\User;
use App\Models\ClientNote;
use Livewire\Attributes\On;
use App\Models\ClientContact;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ClientsNotesTable extends PowerGridComponent
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
            Detail::make()
                ->view('livewire.clients.clients-notes-details')
                ->showCollapseIcon(),
        ];
    }

    #[On('refreshData')]
    public function datasource(): Builder
    {
        return ClientNote::query()
            ->where('client_id', $this->clientId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('note')
            ->add('created_by', function ($entry) {
                return User::find($entry->created_by)->name;
            })
            ->add('created_at_formatted', function ($entry) {
                return $entry->created_at->diffForHumans();
            })
            ->add('updated_at_formatted', function ($entry) {
                return $entry->updated_at->diffForHumans();
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Note', 'note')
                ->searchable()
                ->hidden(),

            Column::make('Created By', 'created_by')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Created')
                ->field('created_at_formatted', 'created_at')
                ->sortable(),

            Column::add()
                ->title('Updated')
                ->field('updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }

    public function actions(ClientNote $row): array
    {
        return [
            Button::add('client-notes--button')
                ->slot('<x-modals.trigger
                    id="clients_notes__modal"
                    label="Edit"
                    label-classes="hidden sm:block"
                    icon="edit"
                    class="btn-accent btn-sm"
                    title="Edit Note"
                />')
                ->dispatchTo('clients.clients-notes', 'editNote', ['id' => $row->id]),
            Button::add('client-notes-delete--button')
                ->slot('<x-icons.delete /> Delete')
                ->class('btn btn-sm btn-error uppercase')
                ->dispatchTo('clients.clients-notes', 'deleteNote', ['noteId' => $row->id, 'clientId' => $row->client_id]),
        ];
    }
}
