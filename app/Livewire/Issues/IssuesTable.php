<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Livewire\Attributes\On;
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
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class IssuesTable extends PowerGridComponent
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
            Detail::make()
                ->view('livewire.issues.issues-details')
                ->showCollapseIcon(),
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
            ->add('name')
            ->add('priority')
            ->add('client', fn (Issue $model) => $model->client->name)
            ->add('project', fn (Issue $model) => $model->project->name)
            ->add('created_at_formatted', fn (Issue $model) => $model->created_at->format('m/d/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Priority', 'priority')
                ->searchable()
                ->sortable(),

            Column::make('Client', 'client')
                ->searchable()
                ->sortable(),

            Column::make('Project', 'project')
                ->searchable()
                ->sortable(),

            Column::make('Created', 'created_at_formatted')
                ->hidden(),

            Column::action('Action')
        ];
    }

    public function actions(Issue $row): array
    {
        return [
            Button::add('issue-view--button')
                ->slot('<x-icons.eye />')
                ->route('issues.view', ['issue' => $row->id])
                ->class('btn btn-accent btn-sm')
                ->tooltip('View Issue'),
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }
}
