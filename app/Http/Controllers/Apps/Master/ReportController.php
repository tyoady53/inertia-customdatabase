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
        $a = '';
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $user = User::where('id',$user_id)->first();
        $permissions = $user->getPermissionsViaRoles();
        $select_field = '';$form = ''; $relate = ''; $result = '';$table_to_check = [];$parent = [];$child = [];
        $select = DB::table('master_tables')->where('name',$name)->first();
        $table_name = $select->description;
        $title  = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $relation = DB::table('master_relation')->where('table_name_from',$name)->get();
        $header = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $users = DB::table('users')
            ->join($name, 'users.id', '=', $name.".created_by")
            ->select('users.*')
            ->groupBy($name.".created_by")
            ->get();

        // dd($users);
        if ($title->count() > 0){
			foreach ($title as $index => $t){
				$select_field .= 'id,'.$t->field_name.',';
                if($t->relate_to != ''){
                    if(str_contains($t->input_type, 'Child')){
                        $relations = $t->relate_to;
                        $relate_table = explode('#',$relations);
                        $field_check = $relate_table[1];
                        $check_data = DB::table($relate_table[0])->get();
                        foreach ($check_data as $chk_data){
                            $child[$t->input_type][$field_check] = $check_data;
                        }
                    } else if (str_contains($t->input_type, 'Parent')){
                        $relations = $t->relate_to;
                        $relate_table = explode('#',$relations);
                        $field_check = $relate_table[1];
                        $check_data = DB::table($relate_table[0])->distinct()->get([$field_check]);
                        foreach ($check_data as $chk_data){
                            $parent[$t->input_type][$field_check] = $check_data;
                        }
                    } else {
                        $relations = $t->relate_to;
                        $relate_table = explode('#',$relations);
                        $field_check = $relate_table[1];
                        $check_data = DB::table($relate_table[0])->get([$field_check]);
                        foreach ($check_data as $chk_data){
                            $table_to_check[$t->input_type][$field_check] = $check_data;
                        }
                    }
                }
			}
			$selected = substr($select_field, 0,-1);
            $form = DB::table($name)->selectRaw($selected)->where('status','1')->get();
		} else {
            $form = DB::table($name)->latest()->get();
        }
        $result = array();
        $checklist_data = array();
        $parent_count = 0;
        $parent_data  = array();
        $child_data   = array();
        if($relation->count() > 0){
            foreach ($relation as $rel){
                $result[] = [$rel->field_from => DB::table($rel->table_name_to)->get(),"field_from" => $rel->field_from];
            }$relate = 'yes';
        } else {
            $relate = 'no';
        }
        foreach ($header as $he){
            if(str_contains($he->input_type, 'Parent')){
                $parent_count = +1;
                $parent_data = DB::table(explode('#', $he->relate_to)[0])->select(explode('#', $he->relate_to)[1])->distinct()->get();
                $child_data  = DB::table(explode('#', $he->relate_to)[0])->get();
            }
            if(str_contains($he->input_type, 'Checklist')){
                $checklist_data[explode('#',$he->relate_to)[0]] = [explode('#',$he->relate_to)[0] => DB::table(explode('#',$he->relate_to)[0])->get(),"field_from" => explode('#',$he->relate_to)[1]];
            }
        }
        $field  = DB::table('master_datatype')->get();
        $show_table  = DB::table('master_tables')->where('is_show',1)->get();
        $structures  = DB::table('master_table_structures')->where('is_show',1)->get();

        return Inertia::render('Apps/Report/Show', [
            'group'         => DB::table('master_tablegroup')->get(),
            'table'         => $name,
            'csrfToken'     => csrf_token(),
            'table_name'    => $table_name,
            'title'         => $title,
            'fields'        => $field,
            'headers'       => $header,
            'users'         => $users,
            'forms'         => $form,
            'checklist'     => $table_to_check,
            'child'         => $child,
            'parent'        => $parent,
            'show_table'    => $show_table,
            'relation'      => $relation,
            'related'       => $result,
            'relate'        => $relate,
            'parentData'    => $parent_data,
            'child_data'    => $child_data,
            'parent_count'  => $parent_count,
            'checklist_data'=> $checklist_data,
        ]);
    }

    public function generate_report(Request $request)
    {
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
