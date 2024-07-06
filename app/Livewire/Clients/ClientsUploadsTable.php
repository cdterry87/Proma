<?php

namespace App\Livewire\Clients;

use App\Models\ClientUpload;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ClientsUploadsTable extends PowerGridComponent
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
        return ClientUpload::query()
            ->where('client_id', $this->clientId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('type')
            ->add('size')
            ->add('created_at_formatted', function ($entry) {
                return $entry->created_at->diffForHumans();
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Type', 'type')
                ->searchable()
                ->sortable(),

            Column::make('Size', 'size')
                ->sortable(),

            Column::add()
                ->title('Uploaded')
                ->field('created_at_formatted', 'created_at')
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

    public function actions(ClientUpload $row): array
    {
        return [
            Button::add('file-download--button')
                ->slot('<x-icons.download />')
                ->class('btn btn-secondary btn-sm')
                ->tooltip('Download File')
                ->dispatchTo('clients.clients-uploads-form', 'downloadFile', ['fileId' => $row->id, 'clientId' => $row->client_id]),
            Button::add('file-delete--button')
                ->slot('<x-icons.delete />')
                ->class('btn btn-error btn-sm')
                ->tooltip('Delete File')
                ->dispatchTo('clients.clients-uploads-form', 'deleteFile', ['fileId' => $row->id, 'clientId' => $row->client_id]),
        ];
    }
}
