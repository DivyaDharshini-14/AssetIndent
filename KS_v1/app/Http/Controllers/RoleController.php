<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('id', 'desc')->paginate(15);

        return view('master.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = $this->permissionsGroupedByModule();

        return view('master.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $permissions = $this->expandPermissionAliases($data['permissions'] ?? []);

        $roleData = ['name' => $data['name']];
        if (! empty($data['description'])) {
            $roleData['description'] = $data['description'];
        }

        $role = Role::create($roleData);

        if (! empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role created successfully with '.count($permissions).' permissions assigned.');
    }

    public function edit(Role $role)
    {
        $permissions = $this->permissionsGroupedByModule();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('master.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,'.$role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $permissions = $this->expandPermissionAliases($data['permissions'] ?? []);

        $roleData = ['name' => $data['name']];
        if (! empty($data['description'])) {
            $roleData['description'] = $data['description'];
        } else {
            $roleData['description'] = null;
        }

        $role->update($roleData);
        $role->syncPermissions($permissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role updated successfully with '.count($permissions).' permissions assigned.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.index')->with('success', 'Role deleted.');
    }

    /**
     * When the UI saves a canonical permission (e.g. purchase-requisitions:view),
     * also grant matching aliases (e.g. purchase-requisition.view) so menus/routes work.
     *
     * @param  list<string>  $permissions
     * @return list<string>
     */
    public static function expandPermissionAliases(array $permissions): array
    {
        $selected = array_values(array_unique(array_filter(array_map('strval', $permissions))));
        if ($selected === []) {
            return [];
        }

        $expanded = $selected;

        foreach (self::permissionsGroupedByModule()->flatten(1) as $permission) {
            $canonical = (string) $permission->name;
            $aliases = array_values(array_filter(array_map('strval', $permission->alias_names ?? [])));

            if ($aliases === []) {
                continue;
            }

            $family = array_values(array_unique(array_merge([$canonical], $aliases)));
            if (array_intersect($family, $selected) === []) {
                continue;
            }

            $expanded = array_merge($expanded, $family);
        }

        return array_values(array_unique($expanded));
    }

    /**
     * Group permissions by submodule for Roles UI.
     * Supports both "module:action" and "module.action" naming.
     *
     * @return Collection<string, Collection<int, Permission>>
     */
    public static function permissionsGroupedByModule(): Collection
    {
        return Permission::query()
            ->orderBy('name')
            ->get()
            ->filter(function (Permission $p) {
                if (preg_match('/\.(l1|l2|l3|l4|it_head|finance|manager|department_head)$/i', $p->name)) {
                    return false;
                }
                if (str_starts_with($p->name, 'permissions:') || str_starts_with($p->name, 'permission.')) {
                    return false;
                }
                return true;
            })
            ->groupBy(fn (Permission $p) => self::permissionModuleLabel($p->name))
            ->map(fn (Collection $group) => self::dedupePermissionsByAction($group));
    }

    public static function dedupePermissionsByAction(Collection $permissions): Collection
    {
        $byAction = [];

        foreach ($permissions as $permission) {
            $action = strtolower(self::permissionActionLabel($permission->name));
            if ($action === 'payment') {
                $action = 'pay';
            }
            if ($action === '') {
                continue;
            }

            $byAction[$action][] = $permission;
        }

        return collect($byAction)
            ->map(function (array $items) {
                usort($items, fn (Permission $a, Permission $b) => self::permissionNameRank($b->name) <=> self::permissionNameRank($a->name));

                /** @var Permission $canonical */
                $canonical = $items[0];
                $aliases = collect($items)
                    ->pluck('name')
                    ->filter(fn (string $name) => $name !== $canonical->name)
                    ->values()
                    ->all();
                $canonical->setAttribute('alias_names', $aliases);

                return $canonical;
            })
            ->sortBy('name')
            ->values();
    }

    public static function permissionNameRank(string $name): int
    {
        if (str_contains($name, ':')) {
            return 2;
        }

        if (str_contains($name, '.')) {
            return 1;
        }

        return 0;
    }

    public static function permissionModuleLabel(string $name): string
    {
        if (str_contains($name, ':')) {
            $module = explode(':', $name, 2)[0];
        } elseif (str_contains($name, '.')) {
            $parts = explode('.', $name);
            array_pop($parts);
            $module = implode('.', $parts) ?: 'general';
        } else {
            $module = 'general';
        }

        return match (true) {
            str_starts_with($module, 'indent') => 'Procurement — Indents',
            str_starts_with($module, 'purchase-requisition') => 'Procurement — Purchase Requisitions',
            str_starts_with($module, 'rfq') => 'Procurement — RFQs',
            str_starts_with($module, 'quotation') => 'Procurement — Quotations',
            str_starts_with($module, 'purchase-order') => 'Procurement — Purchase Orders',
            str_starts_with($module, 'grn') => 'Procurement — GRNs',
            str_starts_with($module, 'payment') => 'Procurement — Payments',
            str_starts_with($module, 'quality-inspection') => 'Procurement — Quality Inspections',
            str_starts_with($module, 'procurement') => 'Procurement — Dashboard & Reports',
            str_starts_with($module, 'purchase-management') => 'Procurement — Dashboard & Reports',
            str_starts_with($module, 'purchase-report') => 'Procurement — Dashboard & Reports',
            str_starts_with($module, 'approval') => 'Procurement — Approval Templates',
            default => ucfirst($module),
        };
    }

    /** Human label for the action part of a permission (supports module:action and module.action). */
    public static function permissionActionLabel(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }

        if (str_contains($name, ':')) {
            $action = explode(':', $name, 2)[1] ?? $name;
        } elseif (str_contains($name, '.')) {
            $action = (string) last(explode('.', $name));
        } else {
            $action = $name;
        }

        $action = trim(str_replace(['-', '_'], ' ', $action));

        return $action !== '' ? ucfirst($action) : $name;
    }
}
