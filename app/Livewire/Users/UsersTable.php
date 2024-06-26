<?php

namespace App\Livewire\Users;

use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class UsersTable extends PowerGridComponent
{
    use WithExport;

    #[On('refreshData')]
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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('email')
            ->add('active', function ($entry) {
                return Team::getActiveCodes()->firstWhere('value', $entry->active)['label'];
            })
            ->add('created_at_formatted', function ($entry) {
                return $entry->created_at->diffForHumans();
            })
            ->add('updated_at_formatted', function ($entry) {
                return $entry->updated_at->diffForHumans();
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

            Column::add()
                ->title('Created')
                ->field('created_at_formatted', 'created_at')
                ->sortable(),

            Column::add()
                ->title('Updated')
                ->field('updated_at_formatted', 'updated_at')
                ->sortable(),


            Column::action('Actions')

        ];
    }

    public function actions(User $row): array
    {
        return [
            Button::add('user-view--button')
                ->slot('<x-icons.eye />')
                ->route('users.view', ['user' => $row->id])
                ->class('btn btn-accent btn-sm')
                ->tooltip('View User'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('active', 'active')
                ->dataSource(Team::getActiveCodes())
                ->optionValue('value')
                ->optionLabel('label'),
        ];
    }
}
