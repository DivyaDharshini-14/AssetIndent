<?php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

foreach (['item.view', 'item.create', 'item.edit', 'item.delete'] as $p) {
    Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
}

$role = Role::where('name', 'Super Admin')->first();
if ($role) {
    $role->givePermissionTo(['item.view', 'item.create', 'item.edit', 'item.delete']);
}
echo "Permissions seeded\n";
