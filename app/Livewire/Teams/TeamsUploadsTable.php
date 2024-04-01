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

    public function actions(TeamUpload $row): array
    {
        return [
            Button::add('file-download--button')
                ->slot('<x-icons.download />')
                ->class('btn btn-accent btn-sm')
                ->tooltip('Download File')
                ->dispatchTo('teams.teams-uploads', 'downloadFile', ['fileId' => $row->id, 'teamId' => $row->team_id]),
            Button::add('file-delete--button')
                ->slot('<x-icons.delete />')
                ->class('btn btn-error btn-sm')
                ->tooltip('Delete File')
                ->dispatchTo('teams.teams-uploads', 'deleteFile', ['fileId' => $row->id, 'teamId' => $row->team_id]),
        ];
    }
}
