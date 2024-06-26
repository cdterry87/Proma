<?php

namespace App\Livewire\Teams;

use App\Models\Team;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class TeamsTable extends PowerGridComponent
{
    use WithExport;

    #[On('refreshData')]
    public function datasource(): ?Collection
    {
        return Team::query()
            ->withCount(['users', 'uploads'])
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
                ->view('livewire.teams.teams-details')
                ->showCollapseIcon(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('users_count')
            ->add('uploads_count')
            ->add('active', function ($entry) {
                return Team::getActiveCodes()->firstWhere('value', $entry->active)['label'];
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

            Column::make('Members', 'users_count')
                ->sortable(),

            Column::make('Uploads', 'uploads_count')
                ->sortable(),

            Column::make('Active', 'active')
                ->sortable(),

            Column::add()
                ->title('Updated')
                ->field('updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::action('Action')
        ];
    }

    public function actions(Team $row): array
    {
        return [
            Button::add('team-view--button')
                ->slot('<x-icons.eye />')
                ->route('teams.view', ['team' => $row->id])
                ->class('btn btn-accent btn-sm')
                ->tooltip('View Team'),
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
