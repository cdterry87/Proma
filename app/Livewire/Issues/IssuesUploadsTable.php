<?php

namespace App\Livewire\Issues;

use App\Models\IssueUpload;
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

final class IssuesUploadsTable extends PowerGridComponent
{
    public $issueId;

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
        return IssueUpload::query()
            ->where('issue_id', $this->issueId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('type')
            ->add('size')
            ->add('created_at_formatted', function ($entry) {
                return $entry->created_at->diffForHumans();
            });
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

            Column::add()
                ->title('Uploaded')
                ->field('created_at_formatted', 'created_at')
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

    public function actions(IssueUpload $row): array
    {
        return [
            Button::add('file-download--button')
                ->slot('<x-icons.download />')
                ->class('btn btn-secondary btn-sm')
                ->tooltip('Download File')
                ->dispatchTo('issues.issues-uploads-form', 'downloadFile', ['fileId' => $row->id, 'issueId' => $row->issue_id]),
            Button::add('file-delete--button')
                ->slot('<x-icons.delete />')
                ->class('btn btn-error btn-sm')
                ->tooltip('Delete File')
                ->dispatchTo('issues.issues-uploads-form', 'deleteFile', ['fileId' => $row->id, 'issueId' => $row->issue_id]),
        ];
    }
}
