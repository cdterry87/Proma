<?php

namespace App\Livewire\Users;

use App\Models\User;
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

use Livewire\Attributes\On;

final class Table extends PowerGridComponent
{
    #[On('refreshTable')]
    public function datasource(): ?Collection
    {
        return User::all();
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
            ->addColumn('email')
            ->addColumn('active', function ($entry) {
                return $entry->active ? 'Yes' : 'No';
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

            Column::make('Email', 'email')
                ->sortable(),

            Column::make('Active', 'active')
                ->sortable(),

            Column::make('Created', 'created_at_formatted'),

            Column::action('Actions')

        ];
    }

    public function actions(User $row): array
    {
        return [
            Button::add('user-edit--button')
                ->slot('<x-modals.trigger
                    id="users_form__modal"
                    label="Edit"
                    icon="edit"
                    class="btn-secondary btn-sm"
                />')
                ->dispatchTo('users.form', 'edit', ['id' => $row->id]),
            Button::add('user-permissions--button')
                ->slot('<x-icons.key /> Permissions')
                ->class('btn btn-sm btn-accent')
                ->dispatch('edit', ['key' => $row->id]),
            //...
        ];
    }
}
