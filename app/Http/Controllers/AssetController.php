<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Asset;
use App\Category;
use App\Department;
use App\Location;
use App\Supplier;
use App\Employee;
use App\User;
use App\UserRole;
use App\UploadedDataDetails;
use DNS1DFacade;
use DNS2DFacade;
use Utility;
use PDF;
use Excel;
use DB;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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

        $validatedData = $this->validate($request, [
            'category'          => 'required',
            'department'        => 'required',
            'location'          => 'required',
            'supplier'          => 'required',
            'property_number'   => 'required',
            'serial_number'     => 'required',
            'employee'          => 'required|numeric',
            'item_description'  => 'required',
            'acquisition_cost'  => 'required|regex:/^[0-9]{1,}(,[0-9]{3})*(\.[0-9]+)*$/',
            'asset_no'          => 'nullable',
            'po_no'             => 'required',
            'mr_number'         => 'required',
            'warranty'          => 'nullable',
            'report_of_waste'   => 'nullable',
            'condition'         => 'nullable',
            'accouting_tag'     => 'nullable',
            'disposal_number'   => 'nullable',
            'date_acquired'     => 'required',
        ]);
        
        if($validatedData){
            //Select box
            $category_id                = Utility::validate_category($request->category);
            $department_id              = Utility::validate_department($request->department);
            $supplier_id                = Utility::validate_supplier($request->supplier);
            $location_id                = Utility::validate_location($request->location);
            $added_by                   = auth()->user()->id; //get the id of the current user

            $accountable_employee_id    = $request->employee;
            $item_description           = $request->item_description;
            $acquisition_cost           = str_replace(',', '',$request->acquisition_cost);
            $asset_number               = $request->asset_no;
            $po_number                  = $request->po_no;
            $mr_number                  = $request->mr_number;
            $warranty                   = $request->warranty;
            $report_of_waste_material   = $request->report_of_waste;
            $disposal_number            = $request->disposal_number;
            $serial_number              = $request->serial_number;
            $property_number            = $request->property_number;
            $condition                  = $request->condition;
            $accounting_tag             = $request->accounting_tag;
            $acquisition_date           = $request->date_acquired;
            $slug_token                 = Utility::generate_unique_token();

            /*Insert data*/
            $asset = new Asset();
            
            // foreign keys
            $asset->accountable_employee_id = $accountable_employee_id;
            $asset->category_id             = $category_id;
            $asset->department_id           = $department_id;
            $asset->supplier_id             = $supplier_id;
            $asset->location_id             = $location_id;
            $asset->added_by                = $added_by;
            
            //mandatory fields 
            $asset->item_description        = $item_description;
            $asset->acquisition_cost        = $acquisition_cost;
            $asset->asset_number            = $asset_number;
            $asset->po_number               = $po_number;
            $asset->mr_number               = $mr_number;
            $asset->warranty                = $warranty;
            $asset->report_of_waste_material= $report_of_waste_material;
            $asset->disposal_number         = $disposal_number;
            $asset->serial_number           = $serial_number;
            $asset->property_number         = $property_number;
            $asset->acquisition_date        = $acquisition_date;
            $asset->condition               = $condition;
            $asset->accounting_tag          = $accounting_tag;
            $asset->slug_token              = $slug_token;
            $is_saved                       = $asset->save();

            if($is_saved){
                $to_select              = array('id');
                $filter                 = [];
                $filter['asset_id']     = null;
                $filter['barcode']      = $property_number;
                $get_barcodes           = UploadedDataDetails::select($to_select)
                                        ->where($filter)
                                        ->get();
                if($get_barcodes){
                    foreach($get_barcodes as $row){
                        $udd            = UploadedDataDetails::where('id', $row->id)->first();
                        $udd->asset_id  = $asset->id;
                        $udd->save();
                    }
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
            'assets.*',
            'assets.id as asset_id',
            'assets.created_at as asset_created_at',
            'assets.updated_at as asset_updated_at',
            'assets.slug_token as asset_slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.condition as condition',
            'assets.accounting_tag as accounting_tag',

            'ea.id as employee_id',
            'ea.user_id as usr_id',
            'ea.first_name as employee_first_name',
            'ea.last_name as employee_last_name',
            'ea.photo as employee_photo',
            'ea.address as employee_address',
            'ea.phone as employee_phone',
            'ea.slug_token as accountable_employee_slug_token',
            'ea.is_active as employee_is_active',
            'eu.email as employee_email',
            'eu.id as user_id',
            'eu.username',
            'eu.slug_token as user_slug_token',
            'eu.created_at as employee_created_at',
            'ed.name as employee_department_name',
            'el.branch_name as employee_location_name',
            
            'e_ab.first_name as added_by_first_name',
            'e_ab.last_name as added_by_last_name',
            'e_ab.slug_token as added_by_slug_token',

            'l.branch_name as location_name',
            's.name as supplier_name',
            'd.name as department_name',
        );

        $data = Asset::where('assets.slug_token', $slug_token)
        ->select($to_select)
        ->join('employees as ea', 'ea.id', '=', 'assets.accountable_employee_id')
        ->join('locations as l', 'l.id', '=', 'assets.location_id')
        ->join('suppliers as s', 's.id', '=', 'assets.supplier_id')
        ->join('departments as d', 'd.id', '=', 'assets.department_id')
        ->join('users as eu', 'eu.id', '=', 'assets.added_by')
        ->leftJoin('employees as e_ab', 'e_ab.user_id', '=', 'eu.id')
        ->join('departments as ed', 'ed.id', '=', 'ea.department_id')
        ->leftJoin('locations as el', 'el.id', '=', 'ea.location_id')
        ->firstOrFail();

        // dd($data);
        $categories = Category::orderBy('name', 'asc')->get();
        $departments = Department::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();
        $employees = Employee::orderBy('first_name', 'asc')->where('is_active', 1)->orderBy('last_name', 'asc')->get();
        $users = User::orderBy('username', 'asc')->get();


        return view('assets.asset', compact('data', 'categories', 'departments', 'suppliers', 'locations', 'employees','users'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
            'category'          => 'required',
            'department'        => 'required',
            'location'          => 'required',
            'supplier'          => 'required',
            'property_number'   => 'required',
            'serial_number'     => 'required',
            'employee'          => 'required|numeric',
            'item_description'  => 'required',
            'acquisition_cost'  => 'required|regex:/^[0-9]{1,}(,[0-9]{3})*(\.[0-9]+)*$/',
            'asset_no'          => 'nullable',
            'po_no'             => 'required',
            'mr_number'         => 'required',
            'warranty'          => 'nullable',
            'report_of_waste'   => 'nullable',
            'disposal_number'   => 'nullable',
            'date_acquired'     => 'required',
            'added_by'          => 'required',
            'condition'         => 'nullable',
            'accouting_tag'     => 'nullable',
        ]);


        if($validatedData){
            
            //Select
            $get_category = Category::where('id', $request->category);
            // Check if exist the input
            if($get_category->count() > 0){
                $category_id = $request->category;
            }else{ //if not exist create new
                $category = new Category();
                $category->name = ucfirst($request->category);
                $category->save();
                $category_id =  $category->id;
            }

            //Select
            $get_department = Department::where('id', $request->department);
            // Check if exist the input
            if($get_department->count() > 0){
                $department_id = $request->department;
            }else{ //if not exist create new
                $department = new Department();
                $department->name = ucfirst($request->department);
                $department->save();
                $department_id =  $department->id;
            }

            //Select
            $get_supplier = Supplier::where('id', $request->supplier);
            // Check if exist the input
            if($get_supplier->count() > 0){
                $supplier_id = $request->supplier;
            }else{ //if not exist create new
                $supplier = new Supplier();
                $supplier->name = ucfirst($request->supplier);
                $supplier->save();
                $supplier_id =  $supplier->id;
            }

            //Select
            $get_location = Location::where('id', $request->location);
            // Check if exist the input
            if($get_location->count() > 0){
                $location_id = $request->location;
            }else{ //if not exist create new
                $location = new Location();
                $location->branch_name = ucfirst($request->location);
                $location->save();
                $location_id =  $location->id;
            }

            // $current_user_id = auth()->user()->id;
            // $get_added = Employee::where('user_id', $current_user_id)->first(array('id'));
            // $added_by = $get_added->id; //get the employee id of the current user

            
            $accountable_employee_id    = $request->employee;
            $item_description           = $request->item_description;
            $acquisition_cost           = str_replace(',', '',$request->acquisition_cost);
            $asset_number               = $request->asset_no;
            $po_number                  = $request->po_no;
            $mr_number                  = $request->mr_number;
            $warranty                   = $request->warranty;
            $added_by                   = $request->added_by;
            $report_of_waste_material   = $request->report_of_waste;
            $disposal_number            = $request->disposal_number;
            $serial_number              = $request->serial_number;
            $property_number            = $request->property_number;
            $acquisition_date           = $request->date_acquired;
            // $slug_token                 = generate_unique_token(); //no need to change the slug token

            /*Insert data*/
            $asset = Asset::where('slug_token', $slug_token)->firstOrFail();

            if($asset->accountable_employee_id != $accountable_employee_id){
                $used_by_history_main   = array();
                $key                    = 0;

                $last_used              = Employee::find($asset->accountable_employee_id);
                if($last_used){
                    $used_by_history_array = array(
                        'key'           => $key,
                        'employee_name' => ucwords($last_used->first_name.' '.$last_used->last_name),
                        'from'          => $asset->acquisition_date,
                        'to'            => date('Y-m-d'),
                        'updated_by'    => auth()->user()->username,
                    );
                    array_push($used_by_history_main, $used_by_history_array);
                    $key++;
                }

                if(strlen($asset->used_by_history) > 5){
                    foreach(json_decode($asset->used_by_history,true) as $h){
                        $retain_history = array(
                            'key'               => $key,
                            'employee_name'     => $h['employee_name'],
                            'from'              => $h['from'],
                            'to'                => $h['to'],
                            'updated_by'        => $h['updated_by'],
                        );
                        $key++;
                        array_push($used_by_history_main, $retain_history);
                    }
                }

                // dd($used_by_history_main);

                $asset->used_by_history = json_encode($used_by_history_main);
            }


            if($asset->property_number != $property_number){
                $to_select              = array('id');
                $filters                = [];
                $filters['barcode']    = $asset->property_number;
                $udd            = UploadedDataDetails::select($to_select)
                                ->where($filters)
                                ->get();
            }else{
                $udd            = null;
            }
            
            // foreign keys
            $asset->accountable_employee_id = $accountable_employee_id;
            $asset->category_id             = $category_id;
            $asset->department_id           = $department_id;
            $asset->supplier_id             = $supplier_id;
            $asset->location_id             = $location_id;
            // $asset->added_by                = $added_by; //this field no need to update
            
            //mandatory fields 
            $asset->item_description        = $item_description;
            $asset->acquisition_cost        = $acquisition_cost;
            $asset->asset_number            = $asset_number;
            $asset->po_number               = $po_number;
            $asset->mr_number               = $mr_number;
            $asset->warranty                = $warranty;
            $asset->added_by                = $added_by;
            $asset->report_of_waste_material= $report_of_waste_material;
            $asset->disposal_number         = $disposal_number;
            $asset->serial_number           = $serial_number;
            $asset->property_number         = $property_number;
            $asset->acquisition_date        = $acquisition_date;
            $asset->condition               = $request->condition;
            $asset->accounting_tag          = $request->accounting_tag;
            $is_saved                       = $asset->save();

            if($is_saved){
                if($udd){
                    foreach($udd as $row){
                        $g_udd              = UploadedDataDetails::where('id', $row->id)->first();
                        $g_udd->asset_id    = null;
                        $g_udd->save();
                    }
                }

                $to_select              = array('id');
                $filter                 = [];
                $filter['asset_id']     = null;
                $filter['barcode']      = $property_number;
                $get_barcodes           = UploadedDataDetails::select($to_select)
                                        ->where($filter)
                                        ->get();

                if($get_barcodes){
                    foreach($get_barcodes as $row){
                        $udd            = UploadedDataDetails::where('id', $row->id)->first();
                        $udd->asset_id  = $asset->id;
                        $udd->save();
                    }
                }
            }

           return redirect()->back()->with('success', 'Successfully Updated.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug_token)
    {
        // dd($slug_token);
        $asset = Asset::where('slug_token', $slug_token)->firstOrFail();
        $asset->delete();

        return redirect('dashboard')->with('success', 'Successfully deleted.');
    }

    public function get_data(Request $request){
        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.accounting_tag',
            'assets.condition',
            'assets.po_number', 
            'assets.slug_token', 
            'assets.acquisition_date as date_acquired',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );
        
        $filters = [];
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $filters['assets.accountable_employee_id'] = Utility::current_employee_id();
        }
        
        $data = Asset::join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
        ->select($to_select)
        ->join('locations', 'locations.id', '=', 'assets.location_id')
        ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
        ->where($filters)
        ->get();

        $data_tables = Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('asset_no', function($row){
                return $row->asset_number;
            })
            ->addIndexColumn()
            ->addColumn('property_no', function($row){
                $url_edit = route('asset.show', ['slug_token'=>$row->slug_token]);
                return '<a data-description="'.$row->property_number.'" href="'.$url_edit.'" title="View Asset">'.$row->property_number.'</a>';
            })
            ->addIndexColumn()
            ->addColumn('item_description', function($row){
                return $row->item_description;
            })
            ->addIndexColumn()
            ->addColumn('acquisition_cost', function($row){
                return number_format($row->acquisition_cost, 2);
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return ucfirst($row->location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);

                return '<a data-description="'.$name.'" href="'.$url_edit.'"  title="View Employee">'.$name.'</a>';
            })
            ->addIndexColumn()
            ->addColumn('po_no', function($row){
                return $row->po_number;
            })
            ->addIndexColumn()
            ->addColumn('supplier', function($row){
                return ucfirst($row->supplier_name);
            })
            ->addIndexColumn()
            ->addColumn('date_acquired', function($row){
                 return Utility::get_date_format($row->date_acquired);
            })
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $response = '<div align="center">';
                $response .= '<button class="btn btn-xs btn-primary" onclick="window.location=\''.route('asset.show', ['slug_token'=>$row->slug_token]).'\'" title="Edit" type="button"><i class="mdi mdi-pencil"></i></button>';
                if(Utility::get_current_role() == '1')
                    $response .= ' <button class="btn btn-xs btn-danger btn-delete_data" data-slug_token="'.$row->slug_token.'" data-asset_id="'.$row->asset_number.'" title="Delete" type="button"><i class="mdi mdi-delete"></i></button>';
                $response .= '</div>';

                return $response;
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired' ,'action'])
            ->make(true);
        
        return $data_tables;
    }

    public function get_print_barcode($slug_token){
        $to_select = array(
            'assets.property_number'
        );

        $data = Asset::where('slug_token', $slug_token)->firstOrFail();
        return view('assets.print_barcode', compact('data'));
    }
    public function generate_pdf_asset(Request $request)
    {
        
        $asset_id = $request->asset_id;
        dd($asset_id);
      
     
        $to_select = array(
            'assets.property_number',
            'assets.item_description',
            'assets.created_at',
            'locations.branch_name',
        );
      
            $data = Asset::where('asset_number',$asset_id)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->latest()
            ->get();
        

        $pdf = PDF::loadView('assets.generate-assets-pdf', compact('data'))->setPaper('A4', 'landscape');
        
        return $pdf->download('Assets_Report_'.date('Ymd').'_'.date('His').'.pdf');
    }

    public function remove_history(Request $request, $slug_token){
        $used_by_history_main = array();
        $key = 0;

        $asset = Asset::where('slug_token', $slug_token)->firstOrFail();
        foreach(json_decode($asset->used_by_history,true) as $h){
            if($h['key'] != $request->history_key){
                $retain_history = array(
                    'key'               => $key,
                    'employee_name'     => $h['employee_name'],
                    'from'              => $h['from'],
                    'to'                => $h['to'],
                    'updated_by'        => $h['updated_by'],
                );
                $key++;
                array_push($used_by_history_main, $retain_history);
            }    

        }
        $asset->used_by_history = json_encode($used_by_history_main);
        $asset->save();

        return redirect()->back()->with('success', 'Successfully Removed.');
    }

}
