<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DbViewerController extends Controller
{
    public function index(Request $request)
    {

        $tables=[];
        $columns=[];
        $rows=[];
        $query='';

        // show tables
        if($request->has('schemashow')){

            $schema=$request->schema;

            $tables = DB::select("
                SELECT TABLE_NAME
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = ?
            ",[$schema]);

        }

        // show columns
        if($request->has('schm')){

            $table=$request->sch;

            $columns = DB::select("SHOW COLUMNS FROM $table");

        }

        // view data
        if($request->has('view')){

            $cname=$request->cname ?: '*';
            $tname=$request->tname;
            $condition=$request->c1name;
            $limit=$request->lname ? " AND ".$request->lname : '';

            $query="SELECT $cname FROM $tname WHERE $condition $limit";

            $rows=DB::select($query);

        }

        // custom sql
        if($request->has('querymas1')){

            DB::statement($request->querymas);

            session()->flash('msg','Data Updated');

        }

        return view('dbviewer',compact(
            'tables',
            'columns',
            'rows',
            'query'
        ));

    }
}
