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
        $filtered = array();
        $name = $request->table;
        $user = $request->user;
        $a = ''; $b = ''; $date = ''; $date_filter = '';;
        $select = DB::table('master_tables')->where('name',$name)->first();
        $header = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $table_name = $select->description;
        if(!is_null($request->start_date) && !is_null($request->end_date)){
            $date = "Periode ".$request->start_date." to ".$request->end_date;
            $date_filter = " AND ".$name.".created_at BETWEEN '".$request->start_date."' AND '".$request->end_date."'";
        }
        if($user){
            $filtered[] = "User`~>`".auth()->user()->name;
            $a = " AND ".$name.".created_by = '".$user."'";
        }
        if($request->check_array){
            for($i = 0; $i < count($request->check_array);$i++){
                foreach($request->data as $index => $d){
                    if($index == $request->check_array[$i]){
                        $select2 = DB::table('master_table_structures')->where('table_id',$select->id)->where('field_name',$request->check_array[$i])->first();
                        $filtered[] = $select2->field_description."`~>`".$d;
                        $b .= " AND ".$request->check_array[$i]." = '".$d."'";
                    }
                }
            }
        }
        $query = "SELECT * FROM users JOIN $name ON users.id = $name.created_by WHERE $name.`status` = 1 $a $b $date_filter;";
        $data =  DB::select($query);
            // dd($query,$name,$filtered,$date_filter,$request);

        return Inertia::render('Apps/Report/Generate', [
            'table'         => $name,
            'csrfToken'     => csrf_token(),
            'table_name'    => $table_name,
            'headers'       => $header,
            'data'          => $data,
            'date'          => $date,
            'filters'       => $filtered,
        ]);
    }
}
