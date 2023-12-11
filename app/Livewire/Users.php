<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Traits\WithSearch;

class Users extends Component
{
    use WithSearch;

    public function render()
    {
        $results = User::query()
            ->when($this->search && strlen($this->search) >= 3, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->orderBy, function ($query) {
                $query->orderBy($this->orderBy, $this->orderType);
            })
            ->paginate($this->perPage);

        return view('livewire.users', [
            'results' => $results,
        ]);
    }
}
