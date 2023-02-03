<?php

namespace App\Http\Controllers\Apps\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ReportController extends Controller
{
    public function index()
    {
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $user = User::where('id',$user_id)->first();
        $sides = Role::where('name', $user->getRoleNames())->with('permissions')->latest()->get();
        $e = '(';
        for ($i = 0; $i < $sides->count(); $i++){
            for ($j = 0; $j < $sides[$i]['permissions']->count(); $j++){
                if(str_contains($sides[$i]['permissions'][$j]['name'], 'index')){
                    if(str_contains($sides[$i]['permissions'][$j]['name'],'form')){
                        $a[] = array(explode('-', explode('.', $sides[$i]['permissions'][$j]['name'])[0])[1]);
                        $e .= "'".explode('-', explode('.', $sides[$i]['permissions'][$j]['name'])[0])[1]."',";
                        $b[]= explode('-', explode('.', $sides[$i]['permissions'][$j]['name'])[0])[1];
                    }
                }
            }
        }
        $select_val = substr($e,0,-1);
        $select_val .= ')';
        $form_access = DB::table('master_tables')->whereIn('name',$a)->get();
        return Inertia::render('Apps/Report/Index', [
            'form_accesses'   => $form_access,
        ]);
    }

    public function show(Request $request, $name)
    {
        $select_field = '';$form = '';
        $select = DB::table('master_tables')->where('name',$name)->first();
        $table_name = $select->description;
        $title  = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $header = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $users = DB::table('users')
            ->join($name, 'users.id', '=', $name.".created_by")
            ->select('users.*')
            ->groupBy($name.".created_by")
            ->get();
        $selected_data = array();
        if ($title->count() > 0){
			foreach ($title as $index => $t){
				$select_field .= 'id,'.$t->field_name.',';
			}
			$selected = substr($select_field, 0,-1);
            $form = DB::table($name)->selectRaw($selected)->where('status','1')->get();
		} else {
            $form = DB::table($name)->latest()->get();
        }
        foreach($header as $head){
            $selected_data[$head->field_name] = DB::table($name)->distinct()->get($head->field_name);
        }
        return Inertia::render('Apps/Report/Show', [
            'group'         => DB::table('master_tablegroup')->get(),
            'table'         => $name,
            'csrfToken'     => csrf_token(),
            'table_name'    => $table_name,
            'title'         => $title,
            'headers'       => $header,
            'users'         => $users,
            'forms'         => $form,
            'selected'      => $selected_data,
        ]);
    }

    public function generate_report(Request $request)
    {
        $a = '';
        if($request->check_array){
            for($i = 0; $i < count($request->check_array);$i++){
                $a .= $request->check_array[$i].', ';
            }
            dd($request,$a,explode('#', substr($request->data_array,0,-1))[0]);
        } else {
            dd($request);
        }
		$table			= $request->table;
		$edit_id		= $request->data_id;
        $select = DB::table('master_tables')->where('name',$request->table)->first();
        $table_head  = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
		$select_field	= '';
		$values	        = '';
		if (count($table_head) > 0){
			foreach ($table_head as $t){
                $fields = $t->field_name;
				if($t->input_type == 'File'){
                    if($request->file($fields)){
                        $file= $request->file($fields)->store('public/'.$table.'-'.$fields);
                        $values .= $t->field_name."='".str_replace('public/', '',$file)."',";
                    }
                } else if($t->input_type == 'Checklist'){
                    if($request->$fields == ''){
                        $values .= $t->field_name."=NULL,";
                    } else {
                        $vehicleString = implode(",", $request->$fields);
                        $values .= $t->field_name."='".$vehicleString."',";
                    }
                } else {
                    $values .= $t->field_name."= '".$request->$fields."',";
                }
			}
			$selected = substr($values, 0,-1);
		}
		$insert = DB::statement("UPDATE $table SET $selected WHERE id='$edit_id';");
		if($insert){
			return redirect()->route('forms.show',$table);
		}
    }
}
