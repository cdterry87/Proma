<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
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
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Table extends PowerGridComponent
{
    use WithExport;

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
        return Issue::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('description')
            ->add('priority')
            ->add('client')
            ->add('team')
            ->add('project')
            ->add('assigned_to_name')
            ->add('created_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Description', 'description')
                ->searchable()
                ->sortable(),

            Column::make('Priority', 'priority')
                ->searchable()
                ->sortable(),

            Column::make('Client', 'client')
                ->searchable()
                ->sortable(),

            Column::make('Team', 'team')
                ->searchable()
                ->sortable(),

            Column::make('Project', 'project')
                ->searchable()
                ->sortable(),

            Column::make('Assigned To', 'assigned_to_name')
                ->searchable()
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->hidden(),

            Column::action('Action')
        ];
    }

    public function actions(Issue $row): array
    {
        return [
            // @todo - Add view button/link. Remove labels and use icons only.
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }
}
