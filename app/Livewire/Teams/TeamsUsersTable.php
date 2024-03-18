<?php

namespace App\Livewire\Teams;

use App\Models\User;
use App\Models\TeamUser;
use Livewire\Attributes\On;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TeamsUsersTable extends PowerGridComponent
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
        return TeamUser::query()
            ->with('user')
            ->where('team_id', $this->teamId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('user.name')
            ->add('user.email')
            ->add('user.title')
            ->add('user.active', function ($entry) {
                return User::getActiveCodes()->firstWhere('value', $entry->user->active)['label'];
            })
            ->add('created_at_formatted', function ($entry) {
                return $entry->created_at->diffForHumans();
            });
    }

    public function columns(): array
    {
        return [
            Column::make('Name', 'user.name')
                ->searchable()
                ->sortable(),

            Column::make('Email', 'user.email')
                ->searchable()
                ->sortable(),

            Column::make('Title', 'user.title')
                ->searchable()
                ->sortable(),

            Column::make('Active', 'user.active')
                ->sortable(),

            Column::add()
                ->title('Added')
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

    public function actions(TeamUser $row): array
    {
        return [
            Button::add('team-users-remove--button')
                ->slot('<x-icons.delete /> Remove')
                ->class('btn btn-sm btn-error')
                ->dispatchTo('teams.teams-users', 'removeMember', ['userId' => $row->user_id, 'teamId' => $row->team_id]),
        ];
    }
}
