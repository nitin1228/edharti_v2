<?php

namespace App\Http\Controllers;

use App\Helpers\UserActionLogHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['create','store','addPermissionToRole','givePermissionToRole']]);
        $this->middleware('permission:update role', ['only' => ['edit','update']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $roles = Role::get();
        // return view('role-permissions.roles.index', ['roles' => $roles]);
        return view('role-permissions.roles.index');
    }

    public function getRoles(Request $request)
    {
        // Get the logged-in user
        $user = Auth::user();
        $query = Role::query()->select('roles.*');
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
                $q->where('roles.name', 'LIKE', "%{$search}%");
            });

            $totalFiltered = $query->count();
        }

        $getRoleData = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();
        $counter = 1; // Initialize counter for auto-increment
        $data = [];
        foreach ($getRoleData as $role) {
            $nestedData['id'] = $counter++; // Auto-incremented ID
            $nestedData['name'] = $role->name;
            $actionHTML = '';
            if (Auth::user()->can('update role')) {
                $actionHTML .= '<a href="' . url('edharti/roles/' . $role->id . '/edit') . '">
                    <button type="button" class="btn btn-primary px-5">Edit</button>
                </a>';
            }
            if (Auth::user()->can('delete role')) {
                $actionHTML .= '<a href="' . url('edharti/roles/' . $role->id . '/delete') . '"> <button type="button" class="btn btn-danger px-5">Delete</button></a>';
            }
            if (Auth::user()->can('create role')) {
                $actionHTML .= '<a href="' . url('edharti/roles/' . $role->id . '/give-permissions') . '"> <button type="button" class="btn btn-warning px-5">Add / Edit Role Permission</button></a>';
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
        return view('role-permissions.roles.create');
        
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
                'unique:roles,name'
            ]
            ]);

            $role = Role::create([
                'name' => $request->name
            ]);

            // Manage user role create action activity lalit on 22/07/24
            $role_link = '<a href="' . url("/roles") . '" target="_blank">' . $role->name . '</a>';
            UserActionLogHelper::UserActionLog('create', url("/roles"), 'roles', "New role " . $role_link . " has been created by " . Auth::user()->name.".");

            return redirect()->route('roles.index')->with('success','Role Created Successfully');

    }

    public function edit(Role $role)
    {
        return view('role-permissions.roles.edit', ['role' => $role]);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,'.$role->id
            ]
        ]);

        $role->update([
            'name' => $request->name
        ]);

        // Manage user role update action activity lalit on 22/07/24
        $role_link = '<a href="' . url("/roles") . '" target="_blank">' . $role->name . '</a>';
        UserActionLogHelper::UserActionLog('update', url("/roles"), 'roles', "Role " . $role_link . " has been updated by " . Auth::user()->name.".");

        return redirect()->route('roles.index')->with('success','Role Updated Successfully');
    }
    
    public function destroy($roleId)
    {
        $role = Role::find($roleId);
        $role->delete();
        // Manage user role delete action activity lalit on 22/07/24
        $role_link = '<a href="' . url("/roles") . '" target="_blank">' . $role->name . '</a>';
        UserActionLogHelper::UserActionLog('delete', url("/roles"), 'roles', "Role " . $role_link . " has been delete by " . Auth::user()->name.".");
        return redirect()->route('roles.index')->with('success','Role Deleted Successfully');
    }


    public function addPermissionToRole($roleId){
        $role = Role::findOrFail($roleId);
        $rolePermission = DB::table('role_has_permissions')
                                ->where('role_has_permissions.role_id',$role->id)
                                ->pluck('role_has_permissions.permission_id')
                                ->all();
        $permissions = Permission::get();
        return view('role-permissions.roles.add-permissions',[
            'role' => $role,
            'permissions' => $permissions,
            'rolePermission' => $rolePermission
        ]);

    }

    public function givePermissionToRole(Request $request, $roleId){
        $request->validate([
            'permission' => 'required'
        ]);

        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permission);
        return redirect()->back()->with('success','Permissions added to role');


    }
}
