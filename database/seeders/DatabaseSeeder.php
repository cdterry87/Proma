<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createAdminUser();
        $this->createPermissions();
    }

    public function createAdminUser()
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'title' => 'Administrator',
            'password' => Hash::make('password'),
        ]);
    }

    public function createPermissions()
    {
        $permissions = [
            // Administrator Permissions
            ['name' => 'administrator', 'label' => 'Administrator', 'section' => 'Administration'],

            // Projects Permissions
            ['name' => 'projects_create', 'label' => 'Create Projects', 'section' => 'Projects'],
            ['name' => 'projects_read', 'label' => 'View Projects', 'section' => 'Projects'],
            ['name' => 'projects_update', 'label' => 'Update Projects', 'section' => 'Projects'],
            ['name' => 'projects_delete', 'label' => 'Delete Projects', 'section' => 'Projects'],
            ['name' => 'projects_manage_tasks', 'label' => 'Manage Tasks', 'section' => 'Projects'],
            ['name' => 'projects_upload_files', 'label' => 'Upload Files', 'section' => 'Projects'],
            ['name' => 'projects_change_status', 'label' => 'Change Status', 'section' => 'Projects'],

            // Issues Permissions
            ['name' => 'issues_create', 'label' => 'Create Issues', 'section' => 'Issues'],
            ['name' => 'issues_read', 'label' => 'View Issues', 'section' => 'Issues'],
            ['name' => 'issues_update', 'label' => 'Update Issues', 'section' => 'Issues'],
            ['name' => 'issues_delete', 'label' => 'Delete Issues', 'section' => 'Issues'],
            ['name' => 'issues_manage_notes', 'label' => 'Manage Notes', 'section' => 'Issues'],
            ['name' => 'issues_upload_files', 'label' => 'Upload Files', 'section' => 'Issues'],
            ['name' => 'issues_change_status', 'label' => 'Change Status', 'section' => 'Issues'],

            // Clients Permissions
            ['name' => 'clients_create', 'label' => 'Create Clients', 'section' => 'Clients'],
            ['name' => 'clients_read', 'label' => 'View Clients', 'section' => 'Clients'],
            ['name' => 'clients_update', 'label' => 'Update Clients', 'section' => 'Clients'],
            ['name' => 'clients_delete', 'label' => 'Delete Clients', 'section' => 'Clients'],
            ['name' => 'clients_manage_contacts', 'label' => 'Manage Contacts', 'section' => 'Clients'],
            ['name' => 'clients_manage_notes', 'label' => 'Manage Notes', 'section' => 'Clients'],
            ['name' => 'clients_upload_files', 'label' => 'Upload Files', 'section' => 'Clients'],
        ];

        Permission::insert($permissions);
    }
}
