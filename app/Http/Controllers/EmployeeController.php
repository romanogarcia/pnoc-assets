<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;

use App\Employee;
use Illuminate\Http\Request;
use DataTables;
use App\User;
use App\Department;
use App\Location;
use Utility;
class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->current_user_id = auth()->user()->id;

            if(!Utility::get_employee_module_access()){
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

        return view('management/employee.index',compact('departments', 'locations'));
    }

    public function get_data(Request $request){
        $to_select = array(
            'e.photo',
            'e.first_name',
            'e.employee_no',
            'e.last_name',
            'e.address',
            'e.phone',
            'e.slug_token', 
            'e.is_active', 
            'e.created_at as date_added',
            'd.name as department_name',
        );

        $data = Employee::from('employees as e')
        ->select($to_select)
        ->leftJoin('departments as d', 'd.id', '=', 'e.department_id')
        ->orderBy('e.created_at', 'desc')
        ->get();

        $data_tables = Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('photo', function($row){
                $url= Utility::get_employee_photo($row->photo);
                return '<a title="View Employee Information" href="'.route('employee.show', ['slug_token'=>$row->slug_token]).'"><img src="'.$url.'"/></a>';
            })
            ->addIndexColumn()
            ->addColumn('name', function($row){
                return ucwords($row->first_name.' '.$row->last_name);
            })
            ->addIndexColumn()
            ->addColumn('employee_no', function($row){
                return $row->employee_no;
            })
            ->addIndexColumn()
            ->addColumn('phone', function($row){
                return $row->phone;
            })
            ->addIndexColumn()
            ->addColumn('address', function($row){
                return ucfirst($row->address);
            })
            ->addIndexColumn()
            ->addColumn('department', function($row){
                return $row->department_name;
            })
            ->addIndexColumn()
            ->addColumn('is_active', function($row){
                if($row->is_active == '1')
                    $response = '<label data-description="YES" class="badge badge-success">YES</label>';
                else
                    $response = '<label data-description="NO" class="badge badge-danger">NO</label>';

                return $response;
            })
            ->addIndexColumn()
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->date_added);
            })
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $response = '<div class="text-center">';
                $response .= '<button class="btn btn-xs btn-primary" onclick="window.location=\''.route('employee.show', ['slug_token'=>$row->slug_token]).'\'" type="button" title="Edit"><i class="mdi mdi-pencil"></i></button>';
                $response .= '</div>';
                return $response;
            })
            ->rawColumns(['photo','name','employee_no','phone','address', 'department', 'is_active', 'date_added', 'action'])
            ->make(true);
        
        return $data_tables;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
            'first_name'        => 'required',
            'last_name'         => 'required',
            'employee_no'       => 'required|unique:employees,employee_no',
            'department'        => 'required',
            'phone'             => 'nullable',
            'address'           => 'nullable',
            'upload_photo'      => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($validatedData){
            // dd($request->department);
            if ($request->hasFile('upload_photo')) {
                $upload_photo       = $request->file('upload_photo');
                $photo              = Utility::generate_unique_token().'.'.$upload_photo->getClientOriginalExtension();
                $path               = Utility::create_new_upload_dir('employees'); // public/uploads/employees/
                $upload_photo->move($path, $photo);
            }
            else
                $photo          =   null;

                
            //Selected Department
            $department_id              = Utility::validate_department($request->department);
            $slug_token                 = Utility::generate_unique_token();
            $is_active                  = 1;

            $employee                   = new Employee();
            $employee->first_name       = $request->first_name;
            $employee->last_name        = $request->last_name;
            $employee->employee_no      = $request->employee_no;
            $employee->phone            = $request->phone;
            $employee->address          = $request->address;
            $employee->slug_token       = $slug_token;
            $employee->department_id    = $department_id;
            $employee->is_active        = $is_active;
            $employee->photo            = $photo;
            $employee->save();

            return redirect()->back()->with('success', 'Successfully Added.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($slug_token)
    {
        
        $to_select = array(
            'e.employee_no',
            'e.first_name as employee_first_name',
            'e.last_name as employee_last_name',
            'e.address as employee_address',
            'e.phone as employee_phone',
            'e.photo as employee_photo',
            'e.is_active as employee_is_active',
            'e.slug_token as employee_slug_token', 
            'e.created_at as date_added',
            'e.updated_at as date_updated',
            'e.department_id as employee_department_id',
            'd.name as employee_department_name',
            'u.slug_token as user_slug_token',
        );

        $data = Employee::from('employees as e')
        ->where('e.slug_token', '=', $slug_token)
        ->select($to_select)
        ->leftJoin('users as u', 'u.id', '=', 'e.user_id')
        ->join('departments as d', 'd.id', '=', 'e.department_id')
        ->firstOrFail();

        $departments = Department::orderBy('name', 'asc')->get();

        return view('management/employee.employee', compact('data', 'departments'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug_token)
    {
        // dd($slug_token);
        // "employee_no" => "2019081602"
        // "first_name" => "Alexander"
        // "last_name" => "Pierce"
        // "phone" => "9230812157"
        // "address" => "Unit 2708 Tycoon Center Pearl Drive Ortigas Center"
        // "department" => null
        // "is_active" => "1"

        $validatedData = $this->validate($request, [
            'first_name'        => 'required',
            'last_name'         => 'required',
            'department'        => 'required',
            'is_active'         => 'required',
            'phone'             => 'nullable',
            'address'           => 'nullable',
        ]);


        $employee = Employee::where('slug_token', $slug_token)->firstOrFail();
        if($employee->employee_no != $request->employee_no){
            $validatedData = $this->validate($request, [
                'employee_no'       => 'required|unique:employees,employee_no',
            ]);
            
            if($validatedData)
                $employee->employee_no      = $request->employee_no;
        }


        if($validatedData){
            $department_id              = Utility::validate_department($request->department);
            $employee->department_id    = $department_id;
            $employee->first_name       = $request->first_name;
            $employee->last_name        = $request->last_name;
            $employee->phone            = $request->phone;
            $employee->address          = $request->address;
            $employee->is_active        = $request->is_active;
            $employee->save();


            return redirect()->back()->with('success', 'Successfully Updated.');
        }
        
    }

    public function upload_new_photo(Request $request, $slug_token){
        // dd($request);
        // dd($slug_token);
        $employee       = Employee::where('slug_token', $slug_token)->firstOrFail();
        $validatedData  = $this->validate($request, [
            'upload_photo'      => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        // dd($validatedData);
        if($validatedData){
            $old_photo              = $employee->photo;

            if ($request->hasFile('upload_photo')) {
                $upload_photo       = $request->file('upload_photo');
                $photo              = Utility::generate_unique_token().'.'.$upload_photo->getClientOriginalExtension();
                $path               = Utility::create_new_upload_dir('employees'); // public/uploads/employees/
                $upload_photo->move($path, $photo);
            }else{
                $photo              = null;
            }

            $employee->photo        = $photo;
            $is_updated             = $employee->save();

            if($is_updated){
                if($old_photo != ''){
                    $old_photo_path = public_path('uploads/employees/'.$old_photo);
                    if(file_exists($old_photo_path)){
                        unlink($old_photo_path);
                    }
                }
            }
            
            return redirect()->back()->with('success', 'Successfully Uploaded.');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
