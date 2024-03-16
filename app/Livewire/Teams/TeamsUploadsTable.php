<?php

namespace App\Livewire\Teams;

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
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TeamsUploadsTable extends PowerGridComponent
{
    public $teamId;

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
        return TeamUpload::query()
            ->where('team_id', $this->teamId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('type')
            ->add('size')
            ->add('created_at_formatted', fn (TeamUpload $model) => Carbon::parse($model->created_at)->format('m/d/Y H:i:s'));
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

    public function actions(TeamUpload $row): array
    {
        return [
            //
        ];
    }
}
