<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\MasterDivision;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    //
    // public function __construct()
    // {
    //     //base role Permission
    //     $a = 'permission:';
    //     $tables = DB::table('master_tables')->get();
    //     //Loop the tables for permissions
    //     for ($j = 0; $j < $tables->count(); $j++){
    //         //get the tables name for later
    //         $name = $tables[$j]->name;
    //         //set string from base permissions with table name
    //         $a .= $name.'.index|'.$name.'.create|'.$name.'.show|'.$name.'.edit|'.$name.'.delete|';
    //     }
    //     //delete last character(pipe)
    //     $role = substr($a, 0,-1);
    //     //middleware role permissions
    //     $this->middleware([$role]);
    // }

    public function index()
    {
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $users = '';
        $a = '';
        $user = User::where('id',$user_id)->first();
        $permissions = $user->getPermissionsViaRoles();
        for ($j = 0; $j < $permissions->count(); $j++){
            if(str_contains($permissions[$j]['name'], 'index')){
                $a .= $permissions[$j]['name'].', ';
            }
        }
        // hidden super admin
        $role_name = $user->getRoleNames();
        if($role_name[0] == 'superadmin'){
            $users = User::when(request()->q, function($users) {
                $users = $users->where('name', 'like', '%' . request()->q . '%');
            })->with('roles')->latest()->paginate(100);;
        } else {
            $users = User::when(request()->q, function($users) {
                $users = $users->where('name', 'like', '%' . request()->q . '%');
            })->whereHas('roles', function ($query) { $query->where('name','!=','superadmin');})->latest()->paginate(100);
        }

        if(str_contains($a, 'users')){
            return Inertia::render('Apps/Users/Index', [
                'users' => $users
            ]);
        }

        return Inertia::render('Apps/Forbidden', [
        ]);
    }

    public function create()
    {
        $roles = Role::all();
        $division = MasterDivision::all();

        return inertia('Apps/Users/Create', [
            'roles'     => $roles,
            'division'  => $division,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request);
        $this->validate($request, [
            'name'      =>  'required',
            'email'     =>  'required|unique:users',
            'password'  =>  'required|confirmed'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'division'  => $request->division,
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('apps.users.index');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $roles = Role::all();

        $division = MasterDivision::all();

        return Inertia::render('Apps/Users/Edit', [
            'user'      => $user,
            'roles'     => $roles,
            'division'  => $division,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name'  => 'required',
            'email' => 'required|unique:users,email,'.$user->id,
            'password'  => 'nullable|confirmed'
        ]);

        if($request->password == '') {

            $user->update([
                'name'  => $request->name,
            ]);
        } else {

            $user->update([
                'name'  => $request->name,
                'password'  => bcrypt($request->password),
            ]);
        }

        $user->syncRoles($request->roles);

        return redirect()->route('apps.users.index');
    }
}
