<?php

namespace App\Livewire\Issues;

use App\Models\IssueTask;
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
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class IssuesTasksTable extends PowerGridComponent
{
    public $issueId;
    public $orderBy = 'updated_at';
    public $orderType = 'desc';

    #[On('refreshData')]
    public function datasource(): ?Collection
    {
        return IssueTask::query()
            ->where('issue_id', $this->issueId)
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
                ->view('livewire.issues.issues-tasks-details')
                ->showCollapseIcon(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('title')
            ->add('description')
            ->add('completed_date')
            ->add('completed_date_formatted', fn (IssueTask $model) => $model->completed_date ? Carbon::parse($model->completed_date)->format('m/d/Y') : 'Incomplete')
            ->add('updated_at')
            ->add('updated_at_formatted', fn (IssueTask $model) => $model->updated_at->diffForHumans());
    }

    public function columns(): array
    {
        return [
            Column::make('Title', 'title')
                ->searchable()
                ->sortable(),

            Column::add()
                ->title('Completed')
                ->field('completed_date_formatted', 'completed_date')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function actions(IssueTask $row): array
    {
        return [
            Button::add('issue-tasks--button')
                ->slot('<x-modals.trigger
                    id="issues_tasks_form__modal"
                    icon="edit"
                    class="btn-accent btn-sm"
                    title="Edit Task"
                />')
                ->dispatchTo('issues.issues-tasks-form', 'editTask', ['id' => $row->id]),
            Button::add('issue-tasks-delete--button')
                ->slot('<x-icons.delete />')
                ->class('btn btn-sm btn-error')
                ->tooltip('Delete Task')
                ->dispatchTo('issues.issues-tasks-form', 'deleteTask', ['taskId' => $row->id, 'issueId' => $row->issue_id]),
        ];
    }

    public function filters(): array
    {
        return [
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
