<?php

namespace App\Livewire\Clients;

use App\Models\Client;
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

final class ClientsTable extends PowerGridComponent
{
    use WithExport;

    #[On('refreshData')]
    public function datasource(): ?Collection
    {
        return Client::query()
            ->withCount(['contacts', 'uploads', 'incomplete_projects', 'unresolved_issues'])
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
                ->view('livewire.clients.clients-details')
                ->showCollapseIcon(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('name')
            ->add('incomplete_projects_count')
            ->add('unresolved_issues_count')
            ->add('contacts_count')
            ->add('uploads_count')
            ->add('active', function ($entry) {
                return Client::getActiveCodes()->firstWhere('value', $entry->active)['label'];
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

            Column::make('Projects', 'incomplete_projects_count')
                ->sortable(),

            Column::make('Issues', 'unresolved_issues_count')
                ->sortable(),

            Column::make('Contacts', 'contacts_count')
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

    public function actions(Client $row): array
    {
        return [
            Button::add('client-view--button')
                ->slot('<x-icons.eye />')
                ->route('clients.view', ['client' => $row->id])
                ->class('btn btn-accent btn-sm')
                ->tooltip('View Client'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('active', 'active')
                ->dataSource(Client::getActiveCodes())
                ->optionValue('value')
                ->optionLabel('label'),
        ];
    }
}
