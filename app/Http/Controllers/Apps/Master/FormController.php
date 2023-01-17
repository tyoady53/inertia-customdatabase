<?php

namespace App\Http\Controllers\Apps\Master;

use App\Http\Controllers\Controller;
use App\Models\master_table;
use App\Models\master_table_structure;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class FormController extends Controller
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
        // dd($form_access);
        return Inertia::render('Apps/Forms/Index', [
            'form_accesses'   => $form_access,
        ]);
    }

    public function show(Request $request, $name)
    {
        $select_field = '';$form = ''; $relate = ''; $result = '';$table_to_check = [];$parent = [];$child = [];
        $select = DB::table('master_tables')->where('name',$name)->first();
        $table_name = $select->description;
        $description = $name.'_description';
        $title  = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $relation = DB::table('master_relation')->where('table_name_from',$name)->get();
        $header = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
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
            $form = DB::table($name)->get();
        }
        $table_name_to = array();
        $result = '';
        if($relation->count() > 0){
            foreach ($relation as $rel){
                $relate_desc = $rel->table_name_to.'_description';
                $table_to = DB::table('master_tables')->where('name',$rel->table_name_to)->first();
                $refer_to = DB::table('master_table_structures')->where('table_id',$table_to->id)->where('field_name',$rel->refer_to)->first();
                $table_name_to[] = array(
                    "table_name_to" => $rel->table_name_to,
                    "refer_to"      => $rel->refer_to
                );
                $result = [$rel->field_from => DB::table($rel->table_name_to)->get()];
            }$relate = 'yes';
        } else {
            $relate = 'no';
        }
        $field  = DB::table('master_datatype')->get();
        $show_table  = DB::table('master_tables')->where('is_show',1)->where('group',$select->group)->get();
        return Inertia::render('Apps/Forms/Show', [
            'group'         => DB::table('master_tablegroup')->get(),
            'table'         => $name,
            'table_role'    => 'form-'.$name.'.create',
            'table_name'    => $table_name,
            'title'         => $title,
            'field'         => $field,
            'headers'       => $header,
            'form'          => $form,
            'checklist'     => $table_to_check,
            'child'         => $child,
            'parent'        => $parent,
            'show_table'    => $show_table,
            'relation'      => $relation,
            'related'       => $result,
            'relate'        => $relate,
        ]);
    }

    public function add_table(Request $request){
        // $user_id = auth()->user()->id;
        // $carbon         = Carbon::now();
        // $table_name     = str_replace(' ','',strtolower($request->name));
        // $group_name     = str_replace(' ','',strtolower($request->table_group));
        // $query          = "CREATE TABLE $table_name (id int(11) NOT NULL AUTO_INCREMENT, created_at timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, updated_at timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        //  created_by int(11) NOT NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, updated_by int(11) NOT NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, status varchar(1) NOT NULL ,updated_by int(11) NOT NULL, status varchar(1) NOT NULL , PRIMARY KEY (`id`) USING BTREE)";

        // DB::statement($query);

        // DB::insert("INSERT INTO master_tables (`group`, `name`, `description`,`is_show`,`created_by`,`updated_by`) VALUES ('$group_name','$table_name','$request->name','1','$user_id','$user_id')");

        // return back()->with('success', 'Table was created');
    }
}