<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use DataTables;
use App\User;
use App\Employee;
use App\Department;
use App\Location;
use App\UserRole;
use App\Role;

use Utility;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->current_user_id = auth()->user()->id;

            if(!Utility::get_user_module_access()){
                abort(403);
            }

            return $next($request);
        });
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $departments    = Department::orderBy('name', 'asc')->get();
        $locations      = Location::orderBy('branch_name', 'asc')->get();
        $roles          = Role::orderBy('role_name', 'asc')->get();

        $to_select_employees = array(
            'e.employee_no',
            'e.first_name',
            'e.last_name',
            'e.id',
        );
        $employees      = Employee::from('employees as e')
        ->select($to_select_employees)
        ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
        ->where('u.id', '=', null)
        ->orderBy('e.employee_no', 'asc')
        ->orderBy('e.first_name', 'asc')
        ->orderBy('e.last_name', 'asc')
        ->get();

        return view('management/user.index',compact('departments', 'locations', 'roles', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function get_data(Request $request)
    {
        $to_select = array(
            'u.id as user_id',
            'u.username',
            'u.email',
            'u.is_locked as account_lock',
            'u.slug_token as user_slug_token',
            'u.created_at as date_added',
            'e.slug_token as employee_slug_token',
        );

        $query = User::from('users as u')
        ->select($to_select)
        ->leftJoin('employees as e', 'e.user_id', '=', 'u.id')
        ->orderBy('u.created_at', 'desc')
        ->get();
        
        $data_tables = Datatables::of($query)
            ->addIndexColumn()
            ->addColumn('username', function($row){
                return $row->username;
            })
            ->addIndexColumn()
            ->addColumn('email', function($row){
                return $row->email;
            })
            ->addIndexColumn()
            ->addColumn('roles', function($row){
                $to_select = array(
                    'r.role_name'
                );

                $roles = UserRole::select($to_select)
                ->from('user_roles as ur')
                ->join('roles as r', 'r.id', '=', 'ur.role_id')
                ->where('ur.user_id', $row->user_id)
                ->get();
                
                $response = '';
                foreach($roles as $role)
                    $response .= ' '.$role->role_name.',';

                if($response != '')
                    $response = rtrim($response, ',');
                
                $response = ucfirst($response);

                return $response;
            })
            ->addIndexColumn()
            ->addColumn('account_lock', function($row){
                if($row->account_lock == '1'){
                    $response = '<label data-description="Yes" class="badge badge-danger">Yes</label>';
                    $response .= ' <button class="btn btn-xs btn-success btn-delete_data float-right" type="button" data-slug_token="'.$row->user_slug_token.'" title="Activate User" data-employee_id="'.$row->username.'" data-status="'.$row->account_lock.'" type="button"><i class="mdi mdi-lock-open"></i></button>';
                }else if($row->account_lock == '0'){
                    $response = '<label data-description="No" class="badge badge-success">No</label>';
                    $response .= ' <button class="btn btn-xs btn-danger btn-delete_data float-right" type="button" data-slug_token="'.$row->user_slug_token.'" title="Deactivate User" data-employee_id="'.$row->username.'" data-status="'.$row->account_lock.'" type="button"><i class="mdi mdi-lock"></i></button>';
                }

                return $response;
            })
            ->addIndexColumn()
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->date_added);
            })          
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $response = '<div class="text-center">';

                if($row->employee_slug_token)
                    $response .= '<button class="btn btn-xs btn-success" type="button" onclick="window.location=\''.route('employee.show', ['slug_token'=>$row->employee_slug_token]).'\'" title="Click to view employee information"><i class="mdi mdi-account-circle"></i></button>';

                $response .= ' <button class="btn btn-xs btn-primary" type="button" onclick="window.location=\''.route('user.show', ['slug_token'=>$row->user_slug_token]).'\'" type="button" title="Edit"><i class="mdi mdi-pencil"></i></button> ';
                // $response .= ' <button class="btn btn-xs btn-danger" type="button"><i class="mdi mdi-delete"></i></button> ';

                $response .= '</div>';

                return $response;
            })
            ->rawColumns(['username','email','roles','account_lock', 'date_added', 'action'])
            ->make(true);
        
        return $data_tables;
        
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);

        $validatedData = $this->validate($request, [
            'email'             =>      'required|email|unique:users',
            'username'          =>      'required|unique:users,username',
            'password'          =>      'required|confirmed',
            'access_role'       =>      'required',
            'employee'          =>      'nullable',
        ]);

        if($request->access_role == '2'){
            $validatedData = $this->validate($request, [
                'employee'      => 'required',
            ]);
        }

        
        if($validatedData){ //insert new user 
            //user values
            $email          =   $request->email;
            $username       =   $request->username;
            $password       =   $request->password;
            $slug_token     =   Utility::generate_unique_token();

            //add new user
            $user = new User();
            $user->email        = $email;
            $user->username     = $username;
            $user->password     = Hash::make($password);
            $user->slug_token   = $slug_token;
            $is_inserted        = $user->save();
            
            if($is_inserted){
                $user_role          = new UserRole();
                $user_role->user_id = $user->id;
                $user_role->role_id = $request->access_role;
                $user_role->save();

                if($request->employee){
                    //add in employee table
                    $employee               = Employee::findOrFail($request->employee);
                    $employee->user_id      =   $user->id;
                    $employee->save();
                }
            }
            
            return redirect()->back()->with('success', 'Successfully Added.');
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug_token)
    {
        $to_select = array(
            'u.created_at as date_added',
            'u.updated_at as date_updated',
            'u.slug_token as user_slug_token',
            'u.username',
            'u.email',
            'u.is_locked as account_lock',
            'u.id as user_id',
            'e.slug_token as employee_slug_token',
            'e.id as employee_id',
            'ur.role_id as employee_role_id',
        );


        $data = User::from('users as u')
        ->select($to_select)
        ->leftJoin('employees as e', 'e.user_id', '=', 'u.id')
        ->leftJoin('user_roles as ur', 'ur.user_id', '=', 'u.id')
        ->where('u.slug_token', '=', $slug_token)
        ->firstOrFail();

        $to_select_role = array(
            'r.role_name',
        );
        
        $user_roles = UserRole::from('user_roles as ur')
        ->select($to_select_role)
        ->join('roles as r', 'r.id', '=', 'ur.role_id')
        ->where('ur.user_id', '=', $data->user_id)
        ->get();
        
        $roles = '';

        if($user_roles){
            foreach($user_roles as $role)
                $roles .= ' '.ucfirst($role->role_name).',';
        }
            
        $roles = rtrim($roles, ',');

        $access_role          = Role::orderBy('role_name', 'asc')->get();

        $to_select_employees = array(
            'e.employee_no',
            'e.first_name',
            'e.last_name',
            'e.id',
        );
        $employees      = Employee::from('employees as e')
        ->select($to_select_employees)
        ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
        ->where('u.id', '=', null)
        ->orWhere('u.id', '=', $data->user_id)
        ->orderBy('e.employee_no', 'asc')
        ->orderBy('e.first_name', 'asc')
        ->orderBy('e.last_name', 'asc')
        ->get();

        return view('management/user.user', compact('data', 'roles', 'access_role', 'employees'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug_token)
    {
        
    }
    
    public function update_status($slug_token){
        // dd($user_id);
        // Find that user_id in users table select only the is_locked column
        $user = User::where('slug_token', $slug_token)->firstOrFail();

        if($user->is_locked == 1){
            $lock = 0;
        }elseif($user->is_locked == 0){
            $lock = 1;
        }
        
        $user->is_locked = $lock;
        $user->save();

        return redirect()->back()->with('success', 'Successfully updated.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug_token)
    {
        // dd($request);
        $validatedData = $this->validate($request, [
            'username'      => 'required',
            'access_role'   => 'required',
            'is_locked'     => 'required',
            'employee'      => 'nullable',
        ]);
        
        if($request->access_role == '2'){
            $validatedData = $this->validate($request, [
                'employee'      => 'required',
            ]);
        }
        

        $user = User::where('slug_token', $slug_token)->firstOrFail();

        $user->is_locked    = $request->is_locked;

        if($user->email != $request->email){
            $validatedData = $this->validate($request, [
                'email'     => 'required|email|unique:users',
            ]);
            $user->email        =   $request->email;
        }
        if($user->username != $request->username){
            $validatedData = $this->validate($request, [
                'username'  => 'required|unique:users,username'
            ]);
            $user->username     =   $request->username;
        }


        if($validatedData){
            $user->save();
            if($request->employee){
                $employee           = Employee::where('user_id', $user->id)->first();
                if($employee){
                    $employee->user_id  = null;
                    $is_saved = $employee->save();
                }else{
                    $is_saved = true;
                }
                
                if($is_saved){
                    $employee           = Employee::find($request->employee);
                    $employee->user_id  = $user->id;
                    $employee->save();
                }
            }


            $user_role = UserRole::where('user_id', $user->id)->firstOrFail();
            if($user_role->role_id != $request->access_role){
                $user_role->role_id = $request->access_role;
                $user_role->save();
            }

            return redirect()->back()->with('success', 'Successfully Updated.');
        }
    }

    public function set_new_password(Request $request, $slug_token){
        $validatedData = $this->validate($request, [
            'password'          =>      'required|confirmed',
        ]);

        $user           = User::where('slug_token', $slug_token)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();
        
        return redirect()->back()->with('success', 'Password Successfully Changed.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug_token)
    {
        
    }

}
