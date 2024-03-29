<?php

namespace App\Livewire\Projects;

use App\Models\ProjectTask;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProjectsTasksTable extends PowerGridComponent
{
    public $projectId;

    #[On('refreshData')]
    public function datasource(): ?Collection
    {
        return ProjectTask::query()
            ->where('project_id', $this->projectId)
            ->get();
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
                ->view('livewire.projects.projects-tasks-details')
                ->showCollapseIcon(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('description')
            ->add('start_date', fn (ProjectTask $model) => $model->start_date ? Carbon::parse($model->start_date)->format('m/d/Y') : 'Not Started')
            ->add('completed_date', fn (ProjectTask $model) => $model->completed_date ? Carbon::parse($model->completed_date)->format('m/d/Y') : 'Incomplete')
            ->add('assigned_to', fn (ProjectTask $model) => $model->assignedTo->name ?? 'N/A');
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Price', 'price')
                ->sortable(),

            Column::make('Created', 'created_at_formatted'),

            Column::action('Action')
        ];
    }
}
