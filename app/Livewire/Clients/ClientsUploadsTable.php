<?php

namespace App\Livewire\Clients;

use App\Models\ClientUpload;
use App\Models\TeamUpload;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
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
            ->add('created_at', fn (ClientUpload $model) => Carbon::parse($model->created_at)->format('m/d/Y H:i:s'));
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

            Column::make('Created at', 'created_at'),

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
            Button::add('download--button')
                ->slot('<x-icons.download /> Download')
                ->class('btn btn-accent btn-sm')
                ->dispatchTo('clients.clients-view', 'download', ['id' => $row->id]),
        ];
    }
}
