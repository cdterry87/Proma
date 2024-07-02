<?php

namespace App\Livewire\Projects;

use App\Models\ProjectTask;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ProjectsTasksTable extends PowerGridComponent
{
    public $projectId;
    public $orderBy = 'due_date';
    public $orderType = 'desc';

    #[On('refreshData')]
    public function datasource(): ?Collection
    {
        return ProjectTask::query()
            ->where('project_id', $this->projectId)
            ->orderBy($this->orderBy, $this->orderType)
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
            ->add('title')
            ->add('description')
            ->add('start_date')
            ->add('start_date_formatted', fn (ProjectTask $model) => $model->start_date ? Carbon::parse($model->start_date)->format('m/d/Y') : 'Not Started')
            ->add('due_date')
            ->add('due_date_formatted', fn (ProjectTask $model) => $model->due_date ? Carbon::parse($model->due_date)->format('m/d/Y') : 'N/A')
            ->add('completed_date')
            ->add('completed_date_formatted', fn (ProjectTask $model) => $model->completed_date ? Carbon::parse($model->completed_date)->format('m/d/Y') : 'Incomplete')
            ->add('updated_at')
            ->add('updated_at_formatted', fn (ProjectTask $model) => $model->updated_at->diffForHumans());
    }

    public function columns(): array
    {
        return [
            Column::make('Title', 'title')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Start Date')
                ->field('start_date_formatted', 'start_date')
                ->sortable(),

            Column::add()
                ->title('Due')
                ->field('due_date_formatted', 'due_date')
                ->sortable(),

            Column::add()
                ->title('Completed')
                ->field('completed_date_formatted', 'completed_date')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function actions(ProjectTask $row): array
    {
        return [
            Button::add('project-tasks--button')
                ->slot('<x-modals.trigger
                    id="projects_tasks_form__modal"
                    icon="edit"
                    class="btn-secondary btn-sm"
                    title="Edit Task"
                />')
                ->dispatchTo('projects.projects-tasks-form', 'editTask', ['id' => $row->id]),
            Button::add('project-tasks-delete--button')
                ->slot('<x-icons.delete />')
                ->class('btn btn-sm btn-error')
                ->tooltip('Delete Task')
                ->dispatchTo('projects.projects-tasks-form', 'deleteTask', ['taskId' => $row->id, 'projectId' => $row->project_id]),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::boolean('start_date')
                ->label('Started', 'Not Started')
                ->builder(function (Builder $query, string $value) {
                    return $value === 'true'
                        ? $query->whereNotNull('start_date')->where('start_date', '<=', now())
                        : $query->whereNull('start_date')->orWhere('start_date', '>', now());
                }),
            Filter::boolean('due_date')
                ->label('Past Due', 'Not Due')
                ->builder(function (Builder $query, string $value) {
                    return $value === 'true'
                        ? $query->whereNotNull('due_date')->where('due_date', '<', now())
                        : $query->whereNull('due_date')->orWhere('due_date', '>=', now());
                }),
            Filter::boolean('completed_date')
                ->label('Completed', 'Incomplete')
                ->builder(function (Builder $query, string $value) {
                    return $value === 'true'
                        ? $query->whereNotNull('completed_date')
                        : $query->whereNull('completed_date');
                }),
        ];
    }
}
