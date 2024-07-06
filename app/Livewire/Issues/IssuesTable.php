<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use Livewire\Attributes\On;
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
        return Issue::query()
            ->where('user_id', auth()->id());
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('priority', fn (Issue $model) => Issue::getPriorityCodes()->firstWhere('value', $model->priority)['label'])
            ->add('client', fn (Issue $model) => $model->client->name)
            ->add('project', fn (Issue $model) => $model->project->name)
            ->add('resolved', fn (Issue $model) => $model->resolved_date ? true : false)
            ->add('resolved_date')
            ->add('resolved_date_formatted', fn (Issue $model) => $model->resolved_date ? Carbon::parse($model->resolved_date)->format('m/d/Y') : 'Open')
            ->add('updated_at')
            ->add('updated_at_formatted', fn (Issue $model) => $model->updated_at->diffForHumans());
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

            Column::add()
                ->title('Resolved')
                ->field('resolved_date_formatted', 'resolved_date')
                ->sortable(),

            Column::add()
                ->title('Last Updated')
                ->field('updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function actions(Issue $row): array
    {
        return [
            Button::add('issue-view--button')
                ->slot('<x-icons.eye />')
                ->route('issues.view', ['issue' => $row->id])
                ->class('btn btn-secondary btn-sm')
                ->tooltip('View Issue'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('priority', 'priority')
                ->dataSource(Issue::getPriorityCodes())
                ->optionValue('value')
                ->optionLabel('label'),
            Filter::boolean('resolved_date')
                ->label('Resolved', 'Unresolved')
                ->builder(function (Builder $query, string $value) {
                    return $value === 'true'
                        ? $query->whereNotNull('resolved_date')->where('resolved_date', '<=', now())
                        : $query->whereNull('resolved_date')->orWhere('resolved_date', '>', now());
                }),
        ];
    }
}
