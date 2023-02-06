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
        /** Initializing variable
         * Variable [a] for filter data by user
         * variable [b] for filter data by field name
         * variable [date] for label date selected title of report
         * variable [date_filter] for filter data by created at
         */
        $a = ''; $b = ''; $date = ''; $date_filter = '';$start_date = '';$end_date = '';
        $select = DB::table('master_tables')->where('name',$name)->first();
        $header = DB::table('master_table_structures')->where('table_id',$select->id)->where('is_show',1)->get();
        $table_name = $select->description;
        // looping selection and title for created at filter
        if(!is_null($request->start_date) && !is_null($request->end_date)){
            $date = "Periode ".$request->start_date." to ".$request->end_date;
            $date_filter = " AND (".$name.".created_at BETWEEN '".$request->start_date."' AND '".$request->end_date."')";
        }

        // looping and selection data for user filter
        if($user){
            $filtered[] = "User`~>`".auth()->user()->name;
            $a = " AND ".$name.".created_by = '".$user."'";
        }

        // looping and selection for field filter
        if($request->check_array){                                  // check is some field checked
            for($i = 0; $i < count($request->check_array);$i++){    // loop data from checklist input
                foreach($request->data as $index => $d){            // looping data from form input
                    if($index == $request->check_array[$i]){        // fetching data checklist input & form input
                        $select2 = DB::table('master_table_structures')->where('table_id',$select->id)->where('field_name',$request->check_array[$i])->first();
                        $filtered[] = $select2->field_description."`~>`".$d;    // for title
                        $b .= " AND ".$request->check_array[$i]." = '".$d."'";  // add selector
                    } else {                                        // exception for loop data where field is date format
                        $select2 = DB::table('master_table_structures')->where('table_id',$select->id)->where('field_name',$request->check_array[$i])->first();
                        if(str_contains($index,$request->check_array[$i])){
                            if(str_contains($index,'start_date#')){
                                $start_date .= $d;                  // start date of field filter
                            } else {
                                $end_date .= $d;                    // end date of field filter
                                $b .= " AND (".$request->check_array[$i]." BETWEEN '".$start_date."' AND '".$end_date."')"; // add selector field date type
                                $filtered[] = $select2->field_description."`~>`".$start_date." to ".$end_date;              // for title
                                $start_date = '';$end_date = '';    // reset the date filter
                            }
                        }
                    }
                }
            }
        }
        $query = "SELECT * FROM users JOIN $name ON users.id = $name.created_by WHERE $name.`status` = 1 $a $b $date_filter;"; // the query selector
        $data =  DB::select($query);
            // dd($query);

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
