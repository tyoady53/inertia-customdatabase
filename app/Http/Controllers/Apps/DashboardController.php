<?php

namespace App\Http\Controllers\Apps;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Role as ModelsRole;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $user = User::where('id',$user_id)->first();
        $sides = Role::where('name', $user->getRoleNames())->when(request()->q, function($sides) {
            $sides = $sides->where('name', 'like', '%index%');//->where('name', 'NOT LIKE', '%dashboard%')->where('name', 'NOT LIKE', '%users%')->where('name', 'NOT LIKE', '%roles%')
        })->with('permissions')->latest()->get();
        for ($i = 0; $i < $sides->count(); $i++){
            for ($j = 0; $j < $sides[$i]['permissions']->count(); $j++){
                if(str_contains($sides[$i]['permissions'][$j]['name'], 'index')){
                    if(!str_contains($sides[$i]['permissions'][$j]['name'],'permissions')){
                        if(!str_contains($sides[$i]['permissions'][$j]['name'],'dashboard')){
                            if(!str_contains($sides[$i]['permissions'][$j]['name'],'users')){
                                if(!str_contains($sides[$i]['permissions'][$j]['name'],'roles')){
                                    $form_access[] = (
                                        explode('.', $sides[$i]['permissions'][$j]['name'])[0]
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        // dd($form_access);
        return Inertia::render('Apps/Dashboard/Index', [
            'form_access'   => $form_access,
        ]);
    }
}
