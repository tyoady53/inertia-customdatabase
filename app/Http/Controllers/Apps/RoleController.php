<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Http\Resources\Resources;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $user = User::where('id',$user_id)->first();
        $ua = request()->server('HTTP_USER_AGENT');

        $role_name = $user->getRoleNames();
        if($role_name[0] == 'superadmin'){
            $roles = Role::when(request()->q, function($roles) {
                $roles = $roles->where('name', 'like', '%' . request()->q . '%');
            })->with('permissions')->latest()->paginate(100);
        } else {
            $roles = Role::where('name','!=','superadmin')->when(request()->q, function($roles) {
                $roles = $roles->where('name', 'like', '%' . request()->q . '%');
            })->with('permissions')->latest()->paginate(100);
        }

        if(stripos($ua,'okhttp') === false){
            return Inertia('Apps/Roles/Index', [
                'roles' => $roles,
            ]);
        } else {
            return new Resources(true, 'List Data Categories', $roles);
        }
    }

    public function create()
    {
        $permissions = Permission::all();

        return inertia('Apps/Roles/Create', [
            'permissions'   => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'          => 'required',
            'permissions'   => 'required'
        ]);

        $role = Role::create(['name' => $request->name]);

        $role->givePermissionTo($request->permissions);

        return redirect()->route('apps.roles.index');
    }

    public function edit($id) 
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        // $forms = Permission::where('name','like','form-%')->get();
        $forms = DB::table('permissions')->where('name','like','form-%')->get();
        // ddd($forms);

        return inertia('Apps/Roles/Edit', [
            'role'          => $role,
            'permissions'   => $permissions,
            'forms'         => $forms,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $this->validate($request, [
            'name'          => 'required',
            'permissions'   => 'required',
        ]);

        $role->update(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        return redirect()->route('apps.roles.index');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()->route('apps.roles.index');
    }
}
