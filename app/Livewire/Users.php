<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Traits\WithAlert;
use App\Traits\WithDrawer;
use App\Traits\WithSearch;
use App\Traits\ConfirmsDeletes;
use Illuminate\Support\Facades\Hash;

class Users extends Component
{
    use ConfirmsDeletes;
    use WithAlert;
    use WithDrawer;
    use WithSearch;

    public $model_id;
    public $name, $title, $email, $password, $password_confirmation;
    public $phone, $phone_ext;

    public function rules()
    {
        return $this->model_id ? [
            'name' => 'required|min:3|max:255',
            'title' => 'nullable|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->model_id,
            'phone' => 'nullable|numeric|digits:10',
            'phone_ext' => 'nullable|numeric|min:1|max:9999',
        ] : [
            'name' => 'required|min:3|max:255',
            'title' => 'nullable|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|confirmed',
            'phone' => 'nullable|numeric|digits:10',
            'phone_ext' => 'nullable|numeric|min:1|max:9999',
        ];
    }

    public function messages()
    {
        return [];
    }

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

    public function save()
    {
        $this->validate();

        if ($this->model_id) {
            User::find($this->model_id)->update([
                'name' => $this->name,
                'title' => $this->title,
                'email' => $this->email,
                'phone' => $this->phone,
                'phone_ext' => $this->phone_ext,
            ]);
        } else {
            User::create([
                'name' => $this->name,
                'title' => $this->title,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'phone' => $this->phone,
                'phone_ext' => $this->phone_ext,
            ]);
        }

        $this->showDrawerAlert('User saved successfully.');

        $this->dispatch('userSaved');
    }

    public function edit($id)
    {
        $user = User::find($id);
        if (!$user) {
            return;
        }

        $this->model_id = $user->id;
        $this->name = $user->name;
        $this->title = $user->title;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $this->phone_ext = $user->phone_ext;

        $this->openDrawer();
    }

    public function delete($id)
    {
        // User::find($id)->delete();

        $this->showAlert('User deleted successfully.');
    }
}
