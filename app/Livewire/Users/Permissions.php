<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use App\Traits\WithModal;
use App\Models\Permission;
use Livewire\Attributes\On;
use App\Models\UserPermission;

class Permissions extends Component
{
    use WithModal;

    public $user_id, $user_name;
    public $selected_permissions = [];

    #[On('edit')]
    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            $this->user_id = $user->id;
            $this->user_name = $user->name;

            $userPermissions = UserPermission::query()
                ->where('user_id', $user->id)
                ->get();
            foreach ($userPermissions as $userPermission) {
                $this->selected_permissions[$userPermission->permission_id] = true;
            }
        }
    }

    public function render()
    {
        // Get available permissions
        $permissions = Permission::query()
            ->orderBy('section', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.users.permissions', [
            'permissions' => $permissions,
        ]);
    }

    public function save()
    {
        // Delete existing permissions
        UserPermission::where('user_id', $this->user_id)->delete();

        // Insert new permissions if the value is checked
        $permissions = [];
        foreach ($this->selected_permissions as $permission => $value) {
            if (!$value) continue;

            $permissions[] = [
                'user_id' => $this->user_id,
                'permission_id' => $permission,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        if ($permissions) {
            UserPermission::insert($permissions);
        }

        // Refresh the data
        $this->dispatch('refreshData');

        session()->flash('success', 'Permissions saved successfully.');
    }
}
