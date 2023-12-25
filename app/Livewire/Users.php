<?php

namespace App\Livewire;

use App\Models\User;
use App\Livewire\Alert;
use Livewire\Component;
use App\Traits\WithDrawer;
use App\Traits\WithSearch;
use App\Traits\ConfirmsDeletes;
use Illuminate\Support\Facades\Hash;

class Users extends Component
{
    use ConfirmsDeletes;
    use WithDrawer;
    use WithSearch;

    public $model_id;
    public $name, $title, $email, $password, $password_confirmation;
    public $phone, $phone_ext;
    public $active = true;

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

        $data = [
            'name' => $this->name,
            'title' => $this->title,
            'email' => $this->email,
            'phone' => $this->phone,
            'phone_ext' => $this->phone_ext,
            'active' => $this->active,
        ];

        if ($this->model_id) {
            User::find($this->model_id)->update($data);
        } else {
            $data['password'] = Hash::make($this->password);
            $result = User::create($data);
            $this->model_id = $result->id;
        }

        $this->showDrawerAlert('User saved successfully.');
    }

    public function edit($id)
    {
        $result = User::find($id);
        if (!$result) {
            return;
        }

        $this->model_id = $result->id;
        $this->name = $result->name;
        $this->title = $result->title;
        $this->email = $result->email;
        $this->phone = $result->phone;
        $this->phone_ext = $result->phone_ext;
        $this->active = !!$result->active;

        $this->openDrawer();
    }

    public function delete($id)
    {
        User::find($id)->delete();

        $this->dispatch('showAlert', 'User deleted successfully.')
            ->to(Alert::class);
    }
}
