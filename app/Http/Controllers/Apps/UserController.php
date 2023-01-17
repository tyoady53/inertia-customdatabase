<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
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
        $a = '';
        $user = User::where('id',$user_id)->first();
        // $sides = Role::where('name', $user->getRoleNames())->when(request()->q, function($sides) {
        //     $sides = $sides->where('name', 'like', '%index%');
        // })->with('permissions') ->latest()->get();
        $permissions = $user->getPermissionsViaRoles();
        for ($j = 0; $j < $permissions->count(); $j++){
            if(str_contains($permissions[$j]['name'], 'index')){
                $a .= $permissions[$j]['name'].', ';
            }
        }
        $users = User::when(request()->q, function($users) {
            $users = $users->where('name', 'like', '%' . request()->q . '%');
        })->with('roles')->latest()->paginate(5);

        if(str_contains($a, 'users')){
            return Inertia::render('Apps/Users/Index', [
                'users' => $users
            ]);
        }

        return Inertia::render('Apps/Forbidden', [
        ]);

        // $user_id = auth()->user()->id;

        // // $a = '';
        // // $user = User::where('id',$user_id)->first();
        // // $user_role = Role::where('name',$user->getRoleNames())->first();
        // // // dd($user->getPermissionsViaRoles());
        // // $sides = Role::where('name', $user->getRoleNames())->when(request()->q, function($sides) {
        // //     $sides = $sides->where('name', 'like', '%index%');
        // // })->with('permissions') ->latest()->get();
        // // // dd($sides);
        // // for ($i = 0; $i < $sides->count(); $i++){
        // //     for ($j = 0; $j < $sides[$i]['permissions']->count(); $j++){
        // //         if(str_contains($sides[$i]['permissions'][$j]['name'], 'index')){
        // //             $a .= $sides[$i]['permissions'][$j]['name'].', ';
        // //             $form_access[] = array(
        // //                 "access_form" => $sides[$i]['permissions'][$j]['name']
        // //             );
        // //         }
        // //         //$sides[$i]['permissions'][$j]['name'].", ";
        // //     }
        // // }

        // $user = User::where('id',$user_id)->first();
        // $permissions = $user->getAllPermissions();
        // dd($permissions);
        // $users = User::when(request()->q, function($users) {
        //     $users = $users->where('name', 'like', '%' . request()->q . '%');
        // })->with('roles')->latest()->paginate(5);

        // // if(str_contains($a, 'users')){
        // //     return Inertia::render('Apps/Users/Index', [
        // //         'users' => $users
        // //     ]);
        // // }

        // return Inertia::render('Apps/Users/Index', [
        //     'users' => $users
        // ]);
    }

    public function create()
    {
        $roles = Role::all();

        return inertia('Apps/Users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name'      =>  'required',
            'email'     =>  'required|unique:users',
            'password'  =>  'required|confirmed'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => bcrypt($request->password)
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('apps.users.index');
    }

    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        $roles = Role::all();

        return Inertia::render('Apps/Users/Edit', [
            'user'  => $user,
            'roles' => $roles
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
