<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SeedItems extends Command
{
    protected $signature = 'seed:items';

    protected $description = 'Seed item master permissions';

    public function handle()
    {
        foreach (['item.view', 'item.create', 'item.edit', 'item.delete'] as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }
        
        $role = Role::where('name', 'Super Admin')->first();
        if ($role) {
            $role->givePermissionTo(['item.view', 'item.create', 'item.edit', 'item.delete']);
        }

        $this->info('Item permissions seeded successfully.');
    }
}
