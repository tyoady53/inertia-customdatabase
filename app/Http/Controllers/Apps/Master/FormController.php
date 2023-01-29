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
use Spatie\Permission\Models\Permission;
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

    public function create_form(Request $request){
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $a = '';
        $user = User::where('id',$user_id)->first();
        $permissions = $user->getPermissionsViaRoles();
        for ($j = 0; $j < $permissions->count(); $j++){
            if(str_contains($permissions[$j]['name'], 'create')){
                $a .= $permissions[$j]['name'].', ';
            }
        }
        $roles = Role::all();

        if(str_contains($a, 'form.create')){
            return inertia('Apps/Forms/Create', [
                'roles' => $roles,
            ]);
        }

        return Inertia::render('Apps/Forbidden', [
        ]);
    }

    public function show(Request $request, $name)
    {
        $request_name = request()->segment(count(request()->segments()));
        $a = '';
        if(auth()){
            $user_id = auth()->user()->id;
        }
        $user = User::where('id',$user_id)->first();
        $permissions = $user->getPermissionsViaRoles();
        if($request_name == 'manage'){
            $role_request = 'manage';
        } else {
            if($request_name == 'add_data'){
                $role_request = 'create';
            } else {
                $role_request = 'index';
            }
            for ($j = 0; $j < $permissions->count(); $j++){
                if(str_contains($permissions[$j]['name'], $role_request)){
                    $a .= $permissions[$j]['name'].', ';
                }
            }
        }
        $select_field = '';$form = ''; $relate = ''; $result = '';$table_to_check = [];$parent = [];$child = [];
        $select = DB::table('master_tables')->where('name',$name)->first();
        $table_name = $select->description;
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
            $form = DB::table($name)->latest()->get();
        }
        $result = array();
        $parent_count = 0;
        $parent_data  = array();
        $child_data   = array();
        // dd($relation);
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
                // dd(explode('#', $he->relate_to)[0]);
                $parent_data = DB::table(explode('#', $he->relate_to)[0])->select(explode('#', $he->relate_to)[1])->distinct()->get();
                $child_data  = DB::table(explode('#', $he->relate_to)[0])->get();
            }
        }
        // dd($parent_count);
        $field  = DB::table('master_datatype')->get();
        $show_table  = DB::table('master_tables')->where('is_show',1)->get();
        $structures  = DB::table('master_table_structures')->where('is_show',1)->get();
        if($role_request == 'manage'){
            return Inertia::render('Apps/Forms/Manage/Manage', [
                'group'         => DB::table('master_tablegroup')->get(),
                'table'         => $name,
                'create_data'   => 'form-'.$name.'.create',
                'edit_data'     => 'form-'.$name.'.edit',
                'delete_data'   => 'form-'.$name.'.delete',
                'csrfToken'     => csrf_token(),
                'table_name'    => $table_name,
                'title'         => $title,
                'fields'        => $field,
                'headers'       => $header,
                'forms'         => $form,
                'checklist'     => $table_to_check,
                'child'         => $child,
                'parent'        => $parent,
                'show_table'    => $show_table,
                'relation'      => $relation,
                'related'       => $result,
                'relate'        => $relate,
                'avail_tables'  => DB::table('master_tables')->where('is_show',1)->where('name','<>',$name)->get(),
                'structures'    => $structures,
                'parent_data'   => $parent_data,
                'child_data'    => $child_data,
                'parent_count'  => $parent_count,
            ]);
        }else {
            if(str_contains($a, $name)){
                switch($request_name){
                    case 'show':
                        return Inertia::render('Apps/Forms/Show', [
                            'group'         => DB::table('master_tablegroup')->get(),
                            'table'         => $name,
                            'create_data'   => 'form-'.$name.'.create',
                            'edit_data'     => 'form-'.$name.'.edit',
                            'delete_data'   => 'form-'.$name.'.delete',
                            'csrfToken'     => csrf_token(),
                            'table_name'    => $table_name,
                            'title'         => $title,
                            'fields'        => $field,
                            'headers'       => $header,
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
                        ]);
                        break;
                    case 'add_data' :
                        return Inertia::render('Apps/Forms/Add_Data', [
                            'group'         => DB::table('master_tablegroup')->get(),
                            'table'         => $name,
                            'create_data'   => 'form-'.$name.'.create',
                            'edit_data'     => 'form-'.$name.'.edit',
                            'delete_data'   => 'form-'.$name.'.delete',
                            'csrfToken'     => csrf_token(),
                            'table_name'    => $table_name,
                            'title'         => $title,
                            'fields'        => $field,
                            'headers'       => $header,
                            'forms'         => $form,
                            'checklist'     => $table_to_check,
                            'child'         => $child,
                            'parent'        => $parent,
                            'show_table'    => $show_table,
                            'relation'      => $relation,
                            'related'       => $result,
                            'relate'        => $relate,
                        ]);
                        break;
                }
            }
            return Inertia::render('Apps/Forbidden', [
            ]);
        }

    }

    public function create(Request $request){
        $user_id = auth()->user()->id;
        $carbon         = Carbon::now();
        $table_name     = str_replace(' ','',strtolower($request->name));
        // dd(count($request->roles));
        $index          = 'form-'.$table_name.'.index';
        $create         = 'form-'.$table_name.'.create';
        $edit           = 'form-'.$table_name.'.edit';
        $delete         = 'form-'.$table_name.'.delete';
        // $group_name     = str_replace(' ','',strtolower($request->table_group));
        $group_name     = "superadmin";
        $query          = "CREATE TABLE $table_name (id int(11) NOT NULL AUTO_INCREMENT, created_at timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP, updated_at timestamp(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
         created_by int(11), updated_by int(11), status varchar(1) NOT NULL, PRIMARY KEY (`id`) USING BTREE)";

        DB::statement($query);

        DB::insert("INSERT INTO master_tables (`group`, `name`, `description`,`is_show`,`created_by`,`updated_by`) VALUES ('$group_name','$table_name','$request->name','1','$user_id','$user_id')");

        $input_index    = Permission::create(['name' => $index,   'guard_name' => 'web']);
        $input_create   = Permission::create(['name' => $create,  'guard_name' => 'web']);
        $input_edit     = Permission::create(['name' => $edit,    'guard_name' => 'web']);
        $create_delete  = Permission::create(['name' => $delete,  'guard_name' => 'web']);

        for($i = 0; $i < count($request->roles); $i++){
            $role_data = Role::where('name',$request->roles[$i])->first();
            DB::table('role_has_permissions')->insert(['permission_id'=>$input_index->id , 'role_id'=>$role_data->id]);
        }

        return redirect()->route('forms.index');
    }

    public function set_relation(Request $request)
    {
		$table			= $request->table;
		$field_name		= $request->field_from;
		$relate_to_table= $request->relate_table;
        // dd($relate_to_table);
		$relate_to_field= $request->refer_to;
        $select = DB::table('master_tables')->where('name',$table)->first();
        $select2 = DB::table('master_tables')->where('id',$relate_to_table)->first();
        $table_from_desc= DB::table('master_tables')->where('name',$table)->first();
        $table_to_desc  = DB::table('master_tables')->where('id',$relate_to_table)->first();
        $field_from_desc= DB::table('master_table_structures')->where('table_id',$select->id)->where('field_name',$field_name)->first();
        $refer_to_desc  = DB::table('master_table_structures')->where('table_id',$select2->id)->where('field_name',$relate_to_field)->first();

		$insert1 = DB::statement("UPDATE master_table_structures SET relation='1' WHERE table_id='$select->id' AND field_name='$field_name';");
		$insert2 = DB::statement("UPDATE master_table_structures SET relation='1' WHERE table_id='$select2->id' AND field_name='$relate_to_field';");
		$insert3 = DB::statement("INSERT INTO master_relation (table_name_from,field_from,table_name_to,refer_to,field_from_desc,table_from_desc,table_to_desc,refer_to_desc)
            VALUES ('$table','$field_name','$table_to_desc->name','$relate_to_field','$field_from_desc->field_description','$table_from_desc->description','$table_to_desc->description','$refer_to_desc->field_description');");
		if($insert1){
			return redirect()->route('forms.manage',$table);
		}
    }

    public function set_parent(Request $request)
    {
		$table			    = $request->table;
		$field_name		    = $request->field_name;
		$child              = $request->child;
		$data_from_table    = $request->data_from_table;
		$parent_reference   = $request->parent_reference;
		$child_data         = $request->child_data;
        $child_reference    = master_table_structure::where('id', $child_data)->first();
        $parent_reference   = master_table_structure::where('id', $parent_reference)->first();
        $table_data         = DB::table('master_tables')->where('id',$data_from_table)->first();
        $child_input    = 'Child#'.$field_name;
        $parent_input   = 'Parent#'.$child;
        $child_relate   = $table_data->name.'#'.$child_reference->field_name;
        $parent_relate  = $table_data->name.'#'.$parent_reference->field_name;
        $child_save = master_table_structure::where('field_name', $child) //update to selected child
                ->update([
                    'input_type'    => $child_input,
                    'relate_to'     => $child_relate
            ]);
        $parent = master_table_structure::where('field_name', $field_name) //update to selected parent
                ->update([
                    'input_type'    => $parent_input,
                    'relate_to'     => $parent_relate
            ]);
		if($child_save){
			return redirect()->route('forms.manage',$table);
		}
    }

    public function create_data(Request $request){
        $table          = $request->table;
        $select         = DB::table('master_tables')->where('name',$request->table)->first();
        $table_head     = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();

        $now            = Carbon::now()->toDateTimeString();
        $auth           = auth()->user()->id;
		$select_field	= 'created_at,updated_at,created_by,updated_by,status,';
		$values	        = '"'.$now.'","'.$now.'",'.$auth.','.$auth.',"1",';

        foreach ($table_head as $t){
            $fields = $t->field_name;
            $select_field .= $t->field_name.',';
            if($t->input_type == 'File'){
                $file= $request->file($fields)->store($table.'-'.$fields);
                $values .= "'".$file."',";
            } else if($t->input_type == 'Checklist'){
                if($request->$fields == ''){
                    $values .= "NULL,";
                } else {
                    $vehicleString = implode(",", $request->$fields);
                    $values .= "'".$vehicleString."',";
                }
            } else {
                $values .= "'".$request->$fields."',";
            }
        }
        $selected = substr($select_field, 0,-1);
        $insert_value = substr($values, 0,-1);

		$insert = DB::statement("INSERT INTO $table ($selected) VALUES ($insert_value);");
		if($insert){
			return redirect()->route('forms.show',$table);
		}
    }

    public function new_field(Request $request)
    {
        if($request->data_type == 'Checklist'){
            $relate_to = $request->table_to.'#'.$request->field_to;
        } else { $relate_to = ''; }
        $field_name = str_replace(' ', '',strtolower($request->name));
        $table_name = $request->table;
        $data_type      = DB::table('master_datatype')->where('name',$request->data_type)->first();
        $table_selected = DB::table('master_tables')->where('name',$table_name)->first();

        $structure = new master_table_structure;
        $structure->table_id            = $table_selected->id;
        $structure->field_name          = $field_name;
        $structure->field_description   = $request->name;
        $structure->is_show             = '1';
        $structure->data_type           = $data_type->data_type;
        $structure->field_name          = $field_name;
        $structure->relation            = '0';
        $structure->input_type          = $request->data_type;
        $structure->relate_to           = $relate_to;
        $structure->created_by          = auth()->user()->id;
        $structure->save();


        $query = "ALTER TABLE `$table_name` ADD `$field_name` $data_type->data_type";

        DB::statement($query);

        return redirect()->route('forms.manage',$table_name);
    }

    public function update_data(Request $request)
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
                        $file= $request->file($fields)->store($table.'-'.$fields);
                        $values .= $t->field_name."='".$file."',";
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

    public function delete_data($table,$id){
		$table_head		= DB::statement("UPDATE $table SET status='0' WHERE id='$id';");
        if($table_head){
            return redirect()->route('forms.show',$table);
        }
	}
}