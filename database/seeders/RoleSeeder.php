<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Role::DEFINITIONS as $slug => $nombre) {
            Role::query()->updateOrCreate(['slug' => $slug], ['nombre' => $nombre]);
        }

        User::query()->whereIn('role', array_keys(Role::DEFINITIONS))->each(function (User $user): void {
            $role = Role::query()->where('slug', $user->role)->firstOrFail();
            $user->roles()->syncWithoutDetaching($role);
        });
    }
}
