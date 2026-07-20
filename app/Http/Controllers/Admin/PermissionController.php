<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminListingService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request, AdminListingService $listing)
    {
        $result = $listing->process(
            Permission::query()->withCount('roles'),
            ['name'],
            [],
            'name',
            'asc'
        );

        return view('admin.permissions.index', $result + ['permissions' => $result['items']]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|regex:/^[a-z0-9]+(\.[a-z0-9-]+)*$/|unique:permissions,name',
        ]);

        Permission::create(['name' => $validated['name'], 'guard_name' => 'web']);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('admin.permissions.index')->with('success', 'Permission deleted.');
    }
}
