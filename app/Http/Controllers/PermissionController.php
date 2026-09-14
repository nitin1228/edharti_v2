<?php

namespace App\Http\Controllers;

use App\Helpers\UserActionLogHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['create','store']]);
        $this->middleware('permission:update permission', ['only' => ['edit','update']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $permissions = Permission::get();
        // return view('role-permissions.permissions.index', ['permissions' => $permissions]);
        return view('role-permissions.permissions.index');
    }

    public function getPermissions(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();
        $query = Permission::query()->select('permissions.*');
        $columns = ['id', 'name'];

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        if ($request->input('order.0.column')) {
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
        } else {
            $order = $columns['1'];
            $dir = 'asc';
        }

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $query->where(function ($q) use ($search) {
                $q->where('permissions.name', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $getPermissionData = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $counter = 1; // Initialize counter for auto-increment
        $data = [];
        foreach ($getPermissionData as $permission) {
            $nestedData['id'] = $counter++; // Auto-incremented ID
            $nestedData['name'] = $permission->name;
            $actionHTML = '';
            if (Auth::user()->can('update permission')) {
                $actionHTML .= '<a href="' . url('edharti/permissions/' . $permission->id . '/edit') . '">
                    <button type="button" class="btn btn-primary px-5">Edit</button>
                </a>';
            }
            if (Auth::user()->can('delete permission')) {
                $actionHTML .= '<a href="' . url('edharti/permissions/' . $permission->id . '/delete') . '"> <button type="button" class="btn btn-danger px-5">Delete</button></a>';
            }
            $nestedData['action'] = $actionHTML;
            $data[] = $nestedData;
        }


        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('role-permissions.permissions.create');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //dd($request);
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name'
            ]
            ]);

            $permission =  Permission::create([
                'name' => $request->name
            ]);

            // Manage user permission create action activity lalit on 22/07/24
            $permission_link = '<a href="' . url("/permissions") . '" target="_blank">' . $permission->name . '</a>';
            UserActionLogHelper::UserActionLog('create', url("/permissions"), 'permissions', "New permission " . $permission_link . " has been created by " . Auth::user()->name.".");

            return redirect()->route('permissions.index')->with('success','Permission Created Successfully');

    }

    public function edit(Permission $permission)
    {
        return view('role-permissions.permissions.edit', ['permission' => $permission]);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,'.$permission->id
            ]
        ]);

        $permission->update([
            'name' => $request->name
        ]);

        // Manage user permission update action activity lalit on 22/07/24
        $permission_link = '<a href="' . url("/permissions") . '" target="_blank">' . $permission->name . '</a>';
        UserActionLogHelper::UserActionLog('update', url("/permissions"), 'permissions', "Permission " . $permission_link . " has been updated by " . Auth::user()->name.".");

        return redirect()->route('permissions.index')->with('success','Permission Updated Successfully');
    }
    
    public function destroy($permissionId)
    {
        $permission = Permission::find($permissionId);
        $permission->delete();
        // Manage user permission delete action activity lalit on 22/07/24
        $permission_link = '<a href="' . url("/permissions") . '" target="_blank">' . $permission->name . '</a>';
        UserActionLogHelper::UserActionLog('delete', url("/permissions"), 'permissions', "Permission " . $permission_link . " has been deleted by " . Auth::user()->name.".");
        return redirect()->route('permissions.index')->with('success','Permission Deleted Successfully');
    }
}
