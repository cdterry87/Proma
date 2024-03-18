<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

final class ProjectsTable extends PowerGridComponent
{
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
                ->view('livewire.projects.projects-details')
                ->showCollapseIcon(),
        ];
    }

    #[On('refreshData')]
    public function datasource(): Collection
    {
        return Project::query()
            ->withCount(['tasks', 'issues'])
            ->get();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('client', fn (Project $model) => $model->client->name ?? 'N/A')
            ->add('team', fn (Project $model) => $model->team->name ?? 'N/A')
            ->add('due_date', fn (Project $model) => $model->due_date ? Carbon::parse($model->due_date)->format('m/d/Y') : 'N/A')
            ->add('assigned_to', fn (Project $model) => $model->assignedTo->name ?? 'N/A')
            ->add('tasks_count')
            ->add('issues_count')
            ->add('updated_at_formatted', fn (Project $model) => $model->updated_at->diffForHumans());
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Client', 'client')
                ->searchable()
                ->sortable(),

            Column::make('Team', 'team')
                ->searchable()
                ->sortable(),

            Column::make('Due Date', 'due_date')
                ->searchable()
                ->sortable(),

            Column::make('Assigned To', 'assigned_to')
                ->searchable()
                ->sortable(),

            Column::make('Tasks', 'tasks_count')
                ->sortable(),

            Column::make('Issues', 'issues_count')
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

    public function actions(Project $row): array
    {
        return [
            Button::add('project-view--button')
                ->slot('<x-icons.eye /> View')
                ->route('projects.view', ['project' => $row->id],)
                ->class('btn btn-accent btn-sm'),
        ];
    }
}
