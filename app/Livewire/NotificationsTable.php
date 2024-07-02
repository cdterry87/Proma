<?php

namespace App\Livewire;

use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class NotificationsTable extends PowerGridComponent
{
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
                ->view('livewire.notifications-details')
                ->showCollapseIcon(),
        ];
    }

    public function datasource(): Builder
    {
        return UserNotification::query()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('subject')
            ->add('created_at')
            ->add('created_at_formatted', fn (UserNotification $model) => Carbon::parse($model->created_at)->format('m/d/Y H:i:s'))
            ->add('read')
            ->add('read_formatted', fn (UserNotification $model) => $model->read ? 'Yes' : 'No');
    }

    public function columns(): array
    {
        return [
            Column::make('Subject', 'subject')
                ->searchable()
                ->sortable(),

            Column::make('Received', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Read', 'read_formatted', 'read')
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::boolean('read')
                ->label('Read', 'Unread')
                ->builder(function (Builder $query, string $value) {
                    return $value === 'true'
                        ? $query->where('read', true)
                        : $query->where('read', false);
                }),
        ];
    }

    public function afterToggleDetail($id, $state)
    {
        if ($id && $state) {
            $notification = UserNotification::find($id);

            if ($notification) {
                $notification->update(['read' => true]);
            }
        }
    }
}
