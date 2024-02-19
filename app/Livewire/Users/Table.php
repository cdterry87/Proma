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
            // @todo - Add view button/link. Remove labels and use icons only.
            Button::add('user-form--button')
                ->slot('<x-modals.trigger
                    id="users_form__modal"
                    label="Edit"
                    label-classes="hidden sm:block"
                    icon="edit"
                    class="btn-secondary btn-sm"
                    title="Edit User"
                />')
                ->dispatchTo('users.form', 'edit', ['id' => $row->id]),
            Button::add('user-permissions--button')
                ->slot('<x-modals.trigger
                    id="users_permissions__modal"
                    label="Permissions"
                    label-classes="hidden sm:block"
                    icon="key"
                    class="btn-accent btn-sm"
                    title="Edit Permissions"
                />')
                ->dispatchTo('users.permissions', 'edit', ['id' => $row->id]),
        ];
    }
}
