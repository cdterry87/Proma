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
use PowerComponents\LivewirePowerGrid\Responsive;
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
            Header::make()
                ->showSearchInput(),
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
            ->get();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('client', fn (Project $model) => $model->client->name ?? 'N/A')
            ->add('start_date')
            ->add('start_date_formatted', fn (Project $model) => $model->start_date ? Carbon::parse($model->start_date)->format('m/d/Y') : 'Not Started')
            ->add('due_date')
            ->add('due_date_formatted', fn (Project $model) => $model->due_date ? Carbon::parse($model->due_date)->format('m/d/Y') : 'Not Due')
            ->add('completed_date')
            ->add('completed_date_formatted', fn (Project $model) => $model->completed_date ? Carbon::parse($model->completed_date)->format('m/d/Y') : 'Incomplete')
            ->add('updated_at')
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

            Column::add()
                ->title('Start Date')
                ->field('start_date_formatted', 'start_date')
                ->sortable(),

            Column::add()
                ->title('Due Date')
                ->field('due_date_formatted', 'due_date')
                ->sortable(),

            Column::add()
                ->title('Completed')
                ->field('completed_date_formatted', 'completed_date')
                ->sortable(),

            Column::add()
                ->title('Last Updated')
                ->field('updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::action('Action')
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
                        ? $query->whereNotNull('completed_date')->where('completed_date', '<=', now())
                        : $query->whereNull('completed_date')->orWhere('completed_date', '>', now());
                }),
        ];
    }

    public function actions(Project $row): array
    {
        return [
            Button::add('project-view--button')
                ->slot('<x-icons.eye />')
                ->route('projects.view', ['project' => $row->id])
                ->class('btn btn-accent btn-sm')
                ->tooltip('View Project'),
        ];
    }
}
