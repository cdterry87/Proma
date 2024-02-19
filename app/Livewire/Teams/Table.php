<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;

use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class Table extends PowerGridComponent
{
    #[On('refreshTable')]
    public function datasource(): ?Collection
    {
        return Team::all();
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
        ];
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('name')
            ->addColumn('user_count', function ($entry) {
                return $entry->users->count() ?? 0;
            })
            ->addColumn('created_at_formatted', function ($entry) {
                return Carbon::parse($entry->created_at)->format('m/d/Y');
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Members', 'user_count')
                ->sortable(),

            Column::make('Created', 'created_at_formatted'),

            Column::action('Action')
        ];
    }

    public function actions(Team $row): array
    {
        return [
            // @todo - Add view button/link. Remove labels and use icons only.
            Button::add('team-form--button')
                ->slot('<x-modals.trigger
                    id="teams_form__modal"
                    label="Edit"
                    label-classes="hidden sm:block"
                    icon="edit"
                    class="btn-secondary btn-sm"
                    title="Edit Team"
                />')
                ->dispatchTo('teams.form', 'edit', ['id' => $row->id]),
            Button::add('team-users--button')
                ->slot('<x-modals.trigger
                    id="teams_users__modal"
                    label="Members"
                    label-classes="hidden sm:block"
                    icon="users"
                    class="btn-accent btn-sm"
                    title="Edit Members"
                />')
                ->dispatchTo('teams.users', 'edit', ['id' => $row->id]),
        ];
    }
}
