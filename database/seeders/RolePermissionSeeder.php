<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $permissions = [
            'tambah-user',
            'lihat-user',
            'edit-user',
            'hapus-user',
            'tambah-tulisan',
            'lihat-tulisan',
            'edit-tulisan',
            'hapus-tulisan',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles
        $roles = [
            'admin',
            'pengguna',
        ];
        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        // Assign permissions to roles
        $roleAdmin = Role::findByName('admin');
        $roleAdmin->givePermissionTo(['tambah-user', 'lihat-user', 'edit-user', 'hapus-user']);

        $rolePengguna = Role::findByName('pengguna');
        $rolePengguna->givePermissionTo(['tambah-tulisan', 'lihat-tulisan', 'edit-tulisan', 'hapus-tulisan']);
    }

}
