<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Location;
use App\Asset;
use App\Department;
use App\Category;
use App\UploadedDataDetails;
use App\UserRole;
use DataTables;
use Excel;
use PDF;
use Utility;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    
    //ASSETS
    public function assets()
    {

        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.assets', compact('employees', 'locations'));
    }


    public function get_report_asset(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }
        
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }elseif(count($filters)){
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                // ->limit(0)
                ->get();
        }

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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return ucfirst($row->location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);
                return '<a data-description="'.$name.'" href="'.$url_edit.'" title="View Employee">'.$name.'</a>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }

    public function generate_pdf_assets(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        
        $filters = [];
        
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
          
        $to_select = array(
            'assets.property_number',
            'assets.item_description',
            'assets.created_at',
            'locations.branch_name',
        );
      
        if( $date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick );
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
            
            $data = Asset::where($filters)
            ->select($to_select)
            ->whereBetween('assets.acquisition_date',[$from,$to])
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();
        } else {
            $data = Asset::where($filters)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();
        }

        $pdf = PDF::loadView('reports.pdf.generate-assets-pdf', compact('data'))->setPaper('A4', 'landscape');
        
        return $pdf->download('Assets_Report_'.date('Ymd').'_'.date('His').'.pdf');
    }

    public function export_assets(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $location='';
        $employee='';
        $property_no='';
        $count='';
        $row_count='';
        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.location_id',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name as fname',
            'employees.last_name as lname',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        
        $rows           = [];
        $total_amount   = 0;

        foreach($data as $d)
        {
            $rows[]=array(
                'Asset Number'=>$d->asset_number,
                'Property Number' => $d->property_number,
                'Item Description' => $d->item_description,
                'Acquisition Cost' =>number_format($d->acquisition_cost,2),
                'Location'=>$d->location_name,
                'Employee' =>  ucwords($d->fname.' '.$d->lname),
                'PO No.' => $d->po_number,
                'Supplier' => $d->supplier_name,
                'Date Acquired'=>$d->date_acquired,
                'Date Added'=>date('Y-m-d',strtotime($d->created_at)),
            );
            if($accountable_employee_id != '')
                $employee=ucwords($d->fname.' '.$d->lname);
            if($location_id != '')
                $location=$d->location_name;
            if($property_number != '')
               $property_no = $d->property_number;

            $total_amount += $d->acquisition_cost;
            
        }
        if($data->count()==0)
            return redirect(route('report.assets'))->with('error', 'Empty Generated Report.');
        else {
            $count = $data->count();
            $row_count=$count+4;
        }

        $date=date('Y-m-d');
        if( $date_range_pick == ''){
           $from='';
           $to='';
        }


        Excel::create('Asset_report_'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$from,$to,$location,$employee,$property_no,$count,$row_count,$total_amount) {
            $excel->sheet('Asset_sheet', function($sheet) use ($rows,$date,$from,$to,$location,$employee,$property_no,$count,$row_count,$total_amount){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('ASSET REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($employee != '')
                    $sheet->setCellValue('C2', 'Employee: '.$employee);
                if($location != '')
                    $sheet->setCellValue('E2', 'Location: '.$location);
                if($property_no != '')
                    $sheet->setCellValue('G2', 'Property Number: '.$property_no);

                if($from != '' AND $to != '') {
                    $sheet->setCellValue('I2', 'From: ' . $from );
                    $sheet->setCellValue('J2', 'To: ' . $to);
                }

                $sheet->fromArray($rows, null, 'A3', true);
                $sheet->setCellValue('J'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('J'.$row_count,'Total Amount: '.number_format($total_amount, 2));
            });
            
        })->download('xlsx');
    
    }
    

    //MEMORANDUM RECEIPT
    public function memorandum_receipt()
    {
        $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.memorandum_receipt', compact('employees', 'locations'));
    }


    public function generate_pdf_memorandum_receipt(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;

        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_date as date_acquired',
            'assets.acquisition_cost',
            'locations.branch_name',
        );

        $data = Asset::where($filters)      
        ->select($to_select)
        ->join('locations', 'locations.id', '=', 'assets.location_id')
        ->orderBy('assets.created_at', 'desc')
        ->get();

        $pdf = PDF::loadView('reports.pdf.generate-memorandum-pdf', compact('data'))->setPaper('A4', 'landscape');
        
        return $pdf->download('Memorandum_Receipt_Report_'.date('Ymd').'_'.date('His').'.pdf');
    }

    public function export_memorandum_receipt(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $location='';
        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_date as date_acquired',
            'assets.acquisition_cost',
            'locations.branch_name',
        );

        $data = Asset::where($filters)      
        ->select($to_select)
        ->join('locations', 'locations.id', '=', 'assets.location_id')
        ->orderBy('assets.created_at', 'desc')
        ->get();

        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {    
            $rows[]=array(
                'Property No.' => $d->property_number,
                'Item Description' => $d->item_description,
                'Date Acquired' => $d->date_acquired,
                'Location' => $d->branch_name,
                'Acquisition Cost' => number_format($d->acquisition_cost, 2),
            );
            $total_amount += $d->acquisition_cost;
        }
        
        Excel::create('Memorandum_Receipt_report_'.date('Ymd').'_'.date('His'), function($excel) use ($rows) {
            $excel->sheet('Memorandum_Receipt_sheet', function($sheet) use ($rows)
            {
                $sheet->fromArray($rows);
                
            });
            
        })->download('xlsx');

    }

    public function get_memorandum_receipt(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;

        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.slug_token as slug_token',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_date as date_acquired',
            'assets.acquisition_cost',
            'locations.branch_name',
        );


        $data = Asset::where($filters)->select($to_select);
        $data->join('locations', 'locations.id', '=', 'assets.location_id');
        $data->orderBy('assets.created_at', 'desc');

        $data_tables = Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('property_no', function($row){
                $url = route('asset.show', ['slug_token'=>$row->slug_token]);
                return '<a data-description="'.$row->property_number.'" title="View asset" href="'.$url.'" target="_blank">'.$row->property_number.'</a>';
            })
            ->addIndexColumn()
            ->addColumn('item_description', function($row){
                return $row->item_description;
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return $row->branch_name;
            })
            ->addIndexColumn()
            ->addColumn('date_acquired', function($row){
                return Utility::get_date_format($row->date_acquired);
            })
            ->addIndexColumn()
            ->addColumn('acquisition_cost', function($row){
                return number_format($row->acquisition_cost, 2);
            })
            ->rawColumns(['property_no','item_description', 'location','date_acquired', 'acquisition_cost'])
            ->make(true);
        
        return $data_tables;
    }



    //EMPLOYEE LEDGER
    public function employee_ledger()
    {
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        $locations = Location::orderBy('branch_name', 'asc')->get();
        
        return view('reports.employee_ledger', compact('locations','employees'));

    }

    public function generate_pdf_employee_ledger(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;


            $to_select_employee = array(
                'employees.id as employee_id',
                'employees.first_name',
                'employees.last_name',
                'locations.branch_name as office',
            );
            $employee_data = Employee::where('employees.id', '=', $accountable_employee_id)
            ->select($to_select_employee)
            ->join('locations', 'locations.id', '=', 'employees.location_id')
            ->get()
            ->first();

        }

        $to_select = array(
            'assets.slug_token as slug_token',
            'assets.property_number',
            'assets.item_description',
            'assets.mr_number',

            'locations.branch_name as location',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
            ->select($to_select)
            ->whereBetween('assets.acquisition_date',[$from,$to])
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();   
        }else{
            $data = Asset::where($filters)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get(); 
        }

       
        $pdf = PDF::loadView('reports.pdf.generate-ledger-pdf', compact('data', 'employee_data'));
        
        return $pdf->download('Employee_Ledger_Report_'.date('Ymd').'_'.date('His').'.pdf');
    }

    public function export_employee_ledger(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $date_range_pick         = $request->date_range_pick;
        $employee_no             = $request->employee_no;
        $employee='';
        $count='';
        $row_count='';
        $emp_no='';
        $role='';
        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($employee_no != ''){
            if(Employee::where('employees.employee_no','=',$employee_no)->count()==1) {
                $get_employee= Employee::where('employees.employee_no', '=', $employee_no)->select(array('employees.id'))->first();
                $filters['assets.accountable_employee_id'] = $get_employee->id;
                $accountable_employee_id=$get_employee->id;
            }
            else{
                $filters['assets.accountable_employee_id'] = $employee_no;
            }
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.property_number',
            'assets.item_description',
            'assets.mr_number',
            'assets.acquisition_cost',
            'assets.created_at',
            'assets.asset_number',
            'locations.branch_name as location',
            'employees.employee_no',
            'employees.first_name as fname',
            'employees.last_name as lname',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
            ->select($to_select)
            ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();
        }else{
            $data = Asset::where($filters)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();
        }

        if($accountable_employee_id != ''){
            $dept = Employee::where('employees.id','=',$accountable_employee_id)
                ->select('employees.department_id','employees.employee_no','departments.id','departments.name as role')
                ->join('departments', 'departments.id', '=', 'employees.department_id')
                ->first();
            $role=$dept->role;
            $employee_no=$dept->employee_no;
        }
        $role=$dept->role;
        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {    
            $rows[]=array(
                'Property No.' => $d->property_number,
                'Asset Number' => $d->asset_number, 
                'Item Description'=> $d->item_description,
                'Acquisition Cost' => number_format($d->acquisition_cost, 2), 
                'MR No.'=>$d->mr_number,
                'Location'=>$d->location,
                'Date_Added'=> date('Y-m-d',strtotime($d->created_at)),
            );
            if($accountable_employee_id != ''){
                $employee=ucwords($d->fname.' '.$d->lname);

           }
           $total_amount += $d->acquisition_cost;
        }
        if($data->count()==0)
            return redirect(route('report.employee_ledger'))->with('error', 'Empty Generated Report.');
        else {
            $count = $data->count();
            $row_count=$count+7;
        }

        $date=date('Y-m-d');
        $ctr=4;
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Employee_ledger_report'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$ctr,$from,$to,$employee,$count,$row_count,$employee_no,$role,$total_amount) {
            $excel->sheet('Employee_ledger_sheet', function($sheet) use ($rows,$date,$ctr,$from,$to,$employee,$count,$row_count,$employee_no,$role,$total_amount){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->cells('A2', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '12',
                        'bold' => true));
                });
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('PHILIPPINE NATIONAL OIL COMPANY');
                });
                $sheet->cell('A2', function($cell) {
                    $cell->setValue('EMPLOYEE LEDGER CARD');
                });
                $sheet->setCellValue('A3', 'Date: ' . $date);
                if($employee != '') {
                    $sheet->setCellValue('A4', 'Employee: ' . $employee);
                    $sheet->setCellValue('A5', 'Department: ' . $role);
                    $sheet->setCellValue('D4', 'Employee No: ');
                    $sheet->setCellValue('E4',  $employee_no);

                }
                if($from != '' AND $to != '') {
                    $sheet->setCellValue('D3', 'From: ' . $from);
                    $sheet->setCellValue('E3','To: ' . $to);
                }
                $sheet->fromArray($rows, null, 'A6', true);
                $sheet->setCellValue('G'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('G'.$row_count,'Total Amount: '.number_format($total_amount, 2));
            });

        })->download('xlsx');
    }

    public function get_employee_ledger(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $date_range_pick         = $request->date_range_pick;
        $employee_no             = $request->employee_no;

        $filters = [];
        
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }
        
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($employee_no != ''){
            if(Employee::where('employees.employee_no','=',$employee_no)->count()==1) {
                $get_employee= Employee::where('employees.employee_no', '=', $employee_no)->select(array('employees.id'))->first();
                $filters['assets.accountable_employee_id'] = $get_employee->id;
            }
            else{
                $filters['assets.accountable_employee_id'] = $employee_no;
            }
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.slug_token as slug_token',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.asset_number as accounting_tag',
            'assets.mr_number',
            'assets.created_at',
            'locations.branch_name as location',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
            ->select($to_select)
            ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();   
        }elseif(count($filters)){
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            // ->limit(0)
            ->get();
        }
        

        $data_tables = Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('item_description', function($row){
            return $row->item_description;
        })
        ->addIndexColumn()
        ->addColumn('acquisition_cost', function($row){
            return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
         })
        ->addIndexColumn()
        ->addColumn('accounting_tag', function($row){
            return $row->accounting_tag;  //change to accounting tag
        })
        ->addIndexColumn()
        ->addColumn('property_no', function($row){
            $url = route('asset.show', ['slug_token'=>$row->slug_token]);
            return '<a data-description="'.$row->property_number.'" title="View asset" href="'.$url.'" target="_blank">'.$row->property_number.'</a>';
        })
        ->addIndexColumn()
        ->addColumn('mr_no', function($row){
            return $row->mr_number;
        })
        ->addIndexColumn()
        ->addColumn('location', function($row){
            return $row->location;
        })
        ->addIndexColumn()
        ->addColumn('date_added', function($row){
            return Utility::get_date_added_format($row->created_at);
        })

        ->rawColumns(['item_description', 'acquisition_cost', 'accounting_tag', 'property_no', 'mr_no', 'location','date_added'])
        ->make(true);
    
        return $data_tables;
    }

    public function search_employee(Request $request)
    {
        $id = $request->employee;
        $emp_no=$request->employee_no;
            $to_select = array(
                'employees.id as employee_id',
                'employees.first_name',
                'employees.last_name',
                'departments.name as role',
                'employees.employee_no',
            );
            if($id!='') {
                $employee_data = Employee::where('employees.id', '=', $id)
                    ->select($to_select)
                    ->join('departments', 'departments.id', '=', 'employees.department_id')
                    ->get();
           
            }
            if($emp_no!='') {
                $employee_data = Employee::where('employees.employee_no', '=', $emp_no)
                    ->select($to_select)
                    ->join('departments', 'departments.id', '=', 'employees.department_id')
                    ->get();

            }
            foreach ($employee_data as $data){
                    $output_data = "<tr> 
                            <td colspan='2'>
                            <p>Name:    </p> 
                            <p>Department: </p> 
                            </td>
                            <td colspan='2'>
                            <p>".ucwords($data->first_name." ".$data->last_name)."</p>
                            <p>".ucwords($data->role)."</p>
                            </td>
                            <td><p>Employee No.  </p></td>
                            <td>$data->employee_no</td>
                            </tr>";
            }
        
            return $output_data;
        
    }
    public function get_by_location(Request $request){
        if ($request->employee) {
            $to_select = array(
                'employees.id',
                'employees.first_name',
                'employees.last_name',
            );
            $option = '';
            $employees = Employee::where('employees,id', $request->employee)
            ->select($to_select)
            ->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')
            ->get();

            foreach ($employees as $employee) {
                $option .= '<option value="'.$employee->id.'">'.ucfirst($employee->first_name).' '.ucfirst($employee->last_name).'</option>';
            }
            
            return $option;
        }
    }


    //ACTUAL INVENTORY
    public function actual_inventory(){
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        //$employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.actual_inventory', compact('employees', 'locations'));
    }

    public function generate_pdf_actual_inventory(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];

        if ($accountable_employee_id != '') {
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.*',
            'employees.first_name as fname',
            'employees.last_name as lname',
        );

        if ($date_range_pick != '') {
            $date_array  = explode(" - ", $date_range_pick);
            $from = date('Y-m-d', strtotime($date_array[0]));
            $to = date('Y-m-d', strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.acquisition_date', [$from, $to])
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        } else {
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        
    
            $pdf = PDF::loadView('reports.pdf.generate-actual-pdf',compact('data'))->setPaper('A4','landscape');
        
            return $pdf->download('Actual_Inventory_Report_' . date('Ymd') . '_' . date('His').'.pdf');
    
    }

    public function export_actual_inventory(Request $request)
    {
        //         dd($request);

        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $location='';
        $filters = [];

        if ($accountable_employee_id != '') {
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name as fname',
            'employees.last_name as lname',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.created_at',[$from,$to])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {
            $rows[]=array(
                'Property Number'=>$d->property_number,
                'Asset Number' => $d->asset_number,
                'Accountable Employee' => ucwords($d->fname.' '.$d->lname),
                'Date Acquired' =>$d->acquisition_date
            );
            if($location_id != '')
                $location=$d->location_name;
            else
                $location='';

            $total_amount += $d->acquisition_cost;
        }

        $date=date('Y-m-d');
        $ctr=4;
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Actual_inventory_report_'.date('Ymd').'_'.date('His'), function($excel) use ($data,$date,$ctr,$from,$to,$location) {
            $excel->sheet('Actual_inventory_sheet', function($sheet) use ($data,$date,$ctr,$from,$to,$location){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('ACTUAL INVENTORY REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($location != '')
                    $sheet->setCellValue('D2', 'Location: '.$location);
                if($from != '' AND $to != '')
                    $sheet->setCellValue('H2', 'From: '.$from .' To: '.$to);
                $sheet->setCellValue('A3', 'Asset Number');
                $sheet->setCellValue('B3', 'Property Number');
                $sheet->setCellValue('C3', 'Item Description');
                $sheet->setCellValue('D3', 'Acquisition Cost');
                $sheet->setCellValue('E3', 'Location');
                $sheet->setCellValue('F3', 'Employee');
                $sheet->setCellValue('G3', 'PO No.');
                $sheet->setCellValue('H3', 'Supplier');
                $sheet->setCellValue('I3', 'Date Acquired');
                $sheet->setCellValue('J3', 'Date Added');
                foreach ($data as $d) {
                    $sheet->setCellValue('A' . $ctr, $d->asset_number);
                    $sheet->setCellValue('B' . $ctr, $d->property_number);
                    $sheet->setCellValue('C' . $ctr, $d->item_description);
                    $sheet->setCellValue('D' . $ctr, $d->acquisition_cost);
                    $sheet->setCellValue('E' . $ctr, $d->location);
                    $sheet->setCellValue('F' . $ctr, ucwords($d->fname.' '.$d->lname));
                    $sheet->setCellValue('G' . $ctr, $d->po_number);
                    $sheet->setCellValue('H' . $ctr, $d->supplier_name);
                    $sheet->setCellValue('I' . $ctr, $d->date_acquired);
                    $sheet->setCellValue('J' . $ctr, date('Y-m-d',strtotime($d->created_at)));
                    $ctr++;
                }
            });

        })->download('xlsx');
    }

    public function get_actual_inventory(Request $request){
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
    
        $filters = [];
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }
    
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }
        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.created_at',[$from,$to])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }




    //DAILY INVENTORY
    public function daily_inventory(){
        
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        //$employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.daily_inventory', compact('employees', 'locations'));
    }

    public function generate_pdf_daily_inventory(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];

        if ($accountable_employee_id != '') {
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.*',
            'employees.first_name as fname',
            'employees.last_name as lname',
        );

        if ($date_range_pick != '') {
            $date_array  = explode(" - ", $date_range_pick);
            $from = date('Y-m-d', strtotime($date_array[0]));
            $to = date('Y-m-d', strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.acquisition_date', [$from, $to])
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        } else {
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        
    
            $pdf = PDF::loadView('reports.pdf.generate-daily-pdf',compact('data'))->setPaper('A4','landscape');
        
            return $pdf->download('Daily_Inventory_Report_' . date('Ymd') . '_' . date('His').'.pdf');
    
    }

    public function export_daily_inventory(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $location='';
        $employee='';
        $property_no='';
        $count='';
        $row_count='';
        $filters = [];
    
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as original_location_name',
            'cl.branch_name as current_location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('uploaded_data_details as udd', 'udd.barcode', '=', 'assets.property_number')
                ->leftJoin('locations as cl', 'cl.id', '=', 'udd.location_id')
                ->whereRaw("udd.created_at >= ? AND udd.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->orderBy('assets.created_at', 'desc')
                ->groupBy('udd.barcode')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('uploaded_data_details as udd', 'udd.barcode', '=', 'assets.property_number')
                ->leftJoin('locations as cl', 'cl.id', '=', 'udd.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->groupBy('udd.barcode')
                ->get();
        }

        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {
            $rows[]=array(
                'Asset Number'              => $d->asset_number,
                'Property Number'           => $d->property_number,
                'Item Description'          => $d->item_description,
                'Acquisition Cost'          => number_format($d->acquisition_cost,2),
                'Current Location'          => $d->current_location_name,
                'Original Location'         => $d->original_location_name,
                'Employee'                  => ucwords($d->fname.' '.$d->lname),
                'PO No.'                    => $d->po_number,
                'Supplier'                  => $d->supplier_name,
                'Date Acquired'             => $d->date_acquired,
                'Date Added'                => date('Y-m-d',strtotime($d->created_at)),
            );

            if ($accountable_employee_id != '')
                $employee=ucwords($d->fname.' '.$d->lname);
            if($location_id != '')
                $location=$d->location_name;
            if ($property_number != '')
                $property_no=$d->property_number;

            $total_amount += $d->acquisition_cost;

        }
        if($data->count()==0)
            return redirect(route('report.daily_inventory'))->with('error', 'Empty Generated Report.');
        else {
            $count = $data->count();
            $row_count=$count+4;
        }
        $date=date('Y-m-d');
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Daily_inventory_report_'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$from,$to,$location,$employee,$property_no,$count,$row_count,$total_amount) {
            $excel->sheet('Daily_inventory_sheet', function($sheet) use ($rows,$date,$from,$to,$location,$employee,$property_no,$count,$row_count,$total_amount){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('DAILY INVENTORY REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($employee != '')
                    $sheet->setCellValue('C2', 'Employee: '.$employee);
                if($location != '')
                    $sheet->setCellValue('E2', 'Location: '.$location);
                if($property_no != '')
                    $sheet->setCellValue('G2', 'Property Number: '.$property_no);

                if($from != '' AND $to != '') {
                    $sheet->setCellValue('I2', 'From: ' . $from);
                    $sheet->setCellValue('J2', 'To: ' . $to);
                }
                $sheet->fromArray($rows, null, 'A3', true);
                $sheet->setCellValue('K'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('K'.$row_count,'Total Amount: '.number_format($total_amount, 2));

            });

        })->download('xlsx');
    }

    public function get_daily_inventory(Request $request){
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
    
        $filters = [];
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }
        
    
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }
        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as original_location_name',
            'cl.branch_name as current_location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('uploaded_data_details as udd', 'udd.barcode', '=', 'assets.property_number')
                ->leftJoin('locations as cl', 'cl.id', '=', 'udd.location_id')
                ->whereRaw("udd.created_at >= ? AND udd.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->orderBy('assets.created_at', 'desc')
                ->groupBy('udd.barcode')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('uploaded_data_details as udd', 'udd.barcode', '=', 'assets.property_number')
                ->leftJoin('locations as cl', 'cl.id', '=', 'udd.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->groupBy('udd.barcode')
                ->get();
        }

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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
            })
            ->addIndexColumn()
            ->addColumn('current_location', function($row){
                return ucfirst($row->current_location_name);
            })
            ->addIndexColumn()
            ->addColumn('original_location', function($row){
                return ucfirst($row->original_location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);
                return '<a data-description="'.$name.'" href="'.$url_edit.'" title="View Employee">'.$name.'</a>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','current_location', 'original_location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }



    //OFFICE ASSETS
    public function office_assets()
    {
        
        $locations = Location::orderBy('branch_name', 'ASC')->get();
        $departments = Department::orderBy('name', 'ASC')->get();

        return view('reports.office_assets', compact('locations', 'departments'));
    }


    public function get_office_assets(Request $request)
    {
        $location_id             = $request->location_id;
        $department_id           = $request->department_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        
        
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($department_id != ''){
            $filters['assets.department_id'] = $department_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }


        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }elseif(count($filters)){
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                // ->limit(0)
                ->get();
        }




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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return ucfirst($row->location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);
                return '<a data-description="'.$name.'" href="'.$url_edit.'" title="View Employee">'.$name.'</a>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }

    public function generate_pdf_office_assets(Request $request){
        
        $location_id             = $request->location_id;
        $department_id           = $request->department_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];

        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($department_id != ''){
            $filters['assets.department_id'] = $department_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.slug_token as slug_token',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_date as date_acquired',
            'assets.acquisition_cost',

            'employees.first_name',
            'employees.last_name',

            'locations.branch_name as location',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
            ->select($to_select)
            ->whereBetween('assets.acquisition_date',[$from,$to])
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();   
        }else{
            $data = Asset::where($filters)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get(); 
        }

        $date = date('Y-m-d');

        $pdf = PDF::loadView('reports.pdf.generate-office-pdf', compact('data'))->setPaper('A4', 'landscape');
        
        return $pdf->download('Office Assets'.date('Ymd').'_'.date('His').'.pdf');
    }

    public function export_office_assets(Request $request)
    {
        $location_id             = $request->location_id;
        $department_id           = $request->department_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $location='';
        $department='';
        $property_no='';
        $filters = [];
        $count='';
        $row_count='';

        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($department_id != ''){
            $filters['assets.department_id'] = $department_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }


        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name as fname',
            'employees.last_name as lname',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
            'departments.name as department_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('departments', 'departments.id', '=', 'assets.department_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('departments', 'departments.id', '=', 'assets.department_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {
            $rows[]=array(
                'Asset Number'=>$d->asset_number,
                'Property Number' => $d->property_number,
                'Item Description' => $d->item_description,
                'Acquisition Cost' =>number_format($d->acquisition_cost,2),
                'Location'=>$d->location_name,
                'Employee' =>  ucwords($d->fname.' '.$d->lname),
                'PO No.' => $d->po_number,
                'Supplier' => $d->supplier_name,
                'Date Acquired'=>$d->date_acquired,
                'Date Added'=>date('Y-m-d',strtotime($d->created_at)),
            );
            if($location_id != '')
                $location=$d->location_name;
            if($department_id != '')
                $department=$d->department_name;
            if($property_number != '')
                $property_no=$d->property_number;
                
            $total_amount += $d->acquisition_cost;
        }
        if($data->count()==0)
            return redirect(route('report.office_assets'))->with('error', 'Empty Generated Report.');
        else {
            $count = $data->count();
            $row_count=$count+4;
        }
        $date=date('Y-m-d');
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Office_assets_report_'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$from,$to,$location,$department,$property_no,$count,$row_count,$total_amount) {
            $excel->sheet('Office_assets_sheet', function($sheet) use ($rows,$date,$from,$to,$location,$department,$property_no,$count,$row_count,$total_amount){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('OFFICE ASSETS REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($department != '')
                    $sheet->setCellValue('C2', 'Department: '.$department);
                if($location != '')
                    $sheet->setCellValue('E2', 'Location: '.$location);
                if($property_no != '')
                    $sheet->setCellValue('G2', 'Property Number: '.$property_no);
                if($from != '' AND $to != '') {
                    $sheet->setCellValue('I2', 'From: ' . $from);
                    $sheet->setCellValue('J2', 'To: ' . $to);
                }

                $sheet->fromArray($rows, null, 'A3', true);
                $sheet->setCellValue('J'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('J'.$row_count,'Total Amount: '.number_format($total_amount, 2));

            });

        })->download('xlsx');
        
    }

    //CATEGORY ASSETS
    public function category_assets()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        //$employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.category_assets', compact('employees', 'locations','categories'));
    }

    public function generate_pdf_category_assets(Request $request)
    {
        $category_id             = $request->category_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
    
        $filters = [];
    
       
        if ($category_id != '') {
            $filters['assets.category_id'] = $category_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }
    
        $to_select = array(
            'categories.name',
            'assets.item_description',
            'assets.property_number',
            'locations.branch_name',
            'assets.created_at',
            
    
    
        );
    
        if ($date_range_pick != '') {
            $date_array  = explode(" - ", $date_range_pick);
            $from = date('Y-m-d', strtotime($date_array[0]));
            $to = date('Y-m-d', strtotime($date_array[1]));
    
            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.acquisition_date', [$from, $to])
                ->join('categories', 'categories.id', '=', 'assets.category_id')
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        } else {
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('categories', 'categories.id', '=', 'assets.category_id')
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }   
        
    
            $pdf = PDF::loadView('reports.pdf.generate-category-pdf',compact('data'))->setPaper('A4','landscape');
        
            return $pdf->download('Category_Asset_Report_' . date('Ymd') . '_' . date('His').'.pdf');
    
        }

    public function export_category_assets(Request $request)
    {
        //         dd($request);

        $category_id             = $request->category_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $location='';
        $category='';
        $property_no='';
        $filters = [];
        $count='';
        $row_count='';


        if ($category_id != '') {
            $filters['assets.category_id'] = $category_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name as fname',
            'employees.last_name as lname',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
            'categories.name as category_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('categories', 'categories.id', '=', 'assets.category_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('categories', 'categories.id', '=', 'assets.category_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {
            $rows[]=array(
                'Asset Number'=>$d->asset_number,
                'Property Number' => $d->property_number,
                'Item Description' => $d->item_description,
                'Acquisition Cost' =>number_format($d->acquisition_cost,2),
                'Location'=>$d->location_name,
                'Employee' =>  ucwords($d->fname.' '.$d->lname),
                'PO No.' => $d->po_number,
                'Supplier' => $d->supplier_name,
                'Date Acquired'=>$d->date_acquired,
                'Date Added'=>date('Y-m-d',strtotime($d->created_at)),
            );
            if ($category_id != '')
                $category = $d->category_name;

            if($location_id != '')
                $location=$d->location_name;

            if ($property_number != '')
                $property_no = $property_number;

            $total_amount += $d->acquisition_cost;
        }
        if($data->count()==0){
            return redirect(route('report.category_assets'))->with('error', 'Empty Generated Report.');
        }
        else {
            $count = $data->count();
            $row_count=$count+4;
        }
        $date=date('Y-m-d');
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Category_assets_report_'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$from,$to,$location,$category,$property_no,$count,$row_count,$total_amount) {
            $excel->sheet('category_assets_sheet', function($sheet) use ($rows,$date,$from,$to,$location,$category,$property_no,$count,$row_count,$total_amount){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('CATEGORY ASSETS REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($category != '')
                    $sheet->setCellValue('C2', 'Category: '.$category);
                if($location != '')
                    $sheet->setCellValue('E2', 'Location: '.$location);
                if($property_no != '')
                    $sheet->setCellValue('G2', 'Property Number: '.$property_no);
                if($from != '' AND $to != '') {
                    $sheet->setCellValue('I2', 'From: ' . $from);
                    $sheet->setCellValue('J2','To: ' . $to);
                }
                $sheet->fromArray($rows, null, 'A3', true);
                $sheet->setCellValue('J'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('J'.$row_count,'Total Amount: '.number_format($total_amount, 2));

            });

        })->download('xlsx');
    }

    public function get_category_assets(Request $request)
    {
        $category_id             = $request->category_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
                
        if($category_id != ''){
            $filters['assets.category_id'] = $category_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.created_at',[$from,$to])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }elseif(count($filters)){
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                // ->limit(0)
                ->get();
        }




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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return ucfirst($row->location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);
                return '<a data-description="'.$name.'" href="'.$url_edit.'" title="View Employee">'.$name.'</a>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }


    //UNLOCATED ITEMS
    public function unlocated_items()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        //$employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.unlocated_items', compact('employees', 'locations','categories'));
    }

    public function generate_pdf_unlocated_items(Request $request)
    {
        $category_id             = $request->category_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
    
        $filters = [];
    
       
        if ($category_id != '') {
            $filters['assets.category_id'] = $category_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }
    
        $to_select = array(
            'categories.name',
            'assets.item_description',
            'assets.property_number',
            'locations.branch_name',
            'assets.created_at',
        );
    
        if ($date_range_pick != '') {
            $date_array  = explode(" - ", $date_range_pick);
            $from = date('Y-m-d', strtotime($date_array[0]));
            $to = date('Y-m-d', strtotime($date_array[1]));
    
            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.acquisition_date', [$from, $to])
                ->join('categories', 'categories.id', '=', 'assets.category_id')
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        } else {
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('categories', 'categories.id', '=', 'assets.category_id')
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }   
        
    
            $pdf = PDF::loadView('reports.pdf.generate-unlocated-pdf',compact('data'))->setPaper('A4','landscape');
        
            return $pdf->download('Unlocated_Items_Report_' . date('Ymd') . '_' . date('His').'.pdf');
    
    }

    public function export_unlocated_items_report(Request $request)
    {
        $location='';
        $property_no='';

        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $count='';
        $row_count='';
        $filters = [];

        if($location_id != ''){
            $filters['a.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['a.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'a.asset_number',
            'a.property_number',
            'a.item_description',
            'a.acquisition_cost',
            'a.po_number',
            'a.slug_token',
            'a.acquisition_date as date_acquired',
            'a.created_at',
            
            'e.slug_token as slug_token_employee',
            'e.first_name',
            'e.last_name',

            'l.branch_name as location_name',
            's.name as supplier_name',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));            

            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->whereRaw("udd.created_at >= ? AND udd.created_at <= ?",[$from.' 00:00:00',$to.' 23:59:59'])
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->get();

        }else{
            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->get();
        }


        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {
            $rows[]=array(
                'Asset Number'=>$d->asset_number,
                'Property Number' => $d->property_number,
                'Item Description' => $d->item_description,
                'Acquisition Cost' =>number_format($d->acquisition_cost,2),
                'Location'=>$d->location_name,
                'Employee' =>  ucwords($d->fname.' '.$d->lname),
                'PO No.' => $d->po_number,
                'Supplier' => $d->supplier_name,
                'Date Acquired'=>$d->date_acquired,
                'Date Added'=>date('Y-m-d',strtotime($d->created_at)),
            );
            
            if($location_id != '')
                $location=$d->location_name;

            if ($property_number != '')
                $property_no = $property_number;

            $total_amount += $d->acquisition_cost;
        }
        if($data->count()==0)
            return redirect(route('report.unlocated_items'))->with('error', 'Empty Generated Report.');
        else {
            $count = $data->count();
            $row_count=$count+4;
        }
        $date=date('Y-m-d');
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Unlocated_items_report'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$from,$to,$location,$property_no,$count,$row_count,$total_amount) {
            $excel->sheet('unlocated_items_sheet', function($sheet) use ($rows,$date,$from,$to,$location,$property_no,$count,$row_count,$total_amount){
                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('UNLOCATED ITEMS REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($location != '')
                    $sheet->setCellValue('E2', 'Location: '.$location);
                if($property_no != '')
                    $sheet->setCellValue('G2', 'Property Number: '.$property_no);
                if($from != '' AND $to != '') {
                    $sheet->setCellValue('I2', 'From: ' . $from);
                    $sheet->setCellValue('J2','To: ' . $to);
                }
                $sheet->fromArray($rows, null, 'A3', true);
                $sheet->setCellValue('J'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('J'.$row_count,'Total Amount: '.number_format($total_amount, 2));
                

            });

        })->download('xlsx');
    }

    public function get_unlocated_items(Request $request)
    {
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
            $filters['a.accountable_employee_id'] = $accountable_employee_id;
        }
        
        if($location_id != ''){
            $filters['a.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['a.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'a.asset_number',
            'a.property_number',
            'a.item_description',
            'a.acquisition_cost',
            'a.po_number',
            'a.slug_token',
            'a.acquisition_date as date_acquired',
            'a.created_at',
            
            'e.slug_token as slug_token_employee',
            'e.first_name',
            'e.last_name',

            'l.branch_name as location_name',
            's.name as supplier_name',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));            

            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->whereRaw("udd.created_at >= ? AND udd.created_at <= ?",[$from.' 00:00:00',$to.' 23:59:59'])
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->get();

        }
        else if(count($filters)){
            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->get();
        }
        else{

            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->limit(0)
            ->get();
        }

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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return ucfirst($row->location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);
                return '<a data-description="'.$name.'" href="'.$url_edit.'" title="View Employee">'.$name.'</a>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }


    //INVENTORY LIST
    public function inventory_list(){
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $employees = Employee::where('user_id', auth()->user()->id)->orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        } else {
            $employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        }
        
        //$employees = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();

        return view('reports.inventory_list', compact('employees', 'locations'));
    }

    public function generate_pdf_inventory_list(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];

        if ($accountable_employee_id != '') {
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if ($location_id != '') {
            $filters['assets.location_id'] = $location_id;
        }
        if ($property_number != '') {
            $filters['assets.property_number'] = $property_number;
        }

        $to_select = array(
            'assets.*',
            'employees.first_name as fname',
            'employees.last_name as lname',
        );

        if ($date_range_pick != '') {
            $date_array  = explode(" - ", $date_range_pick);
            $from = date('Y-m-d', strtotime($date_array[0]));
            $to = date('Y-m-d', strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.acquisition_date', [$from, $to])
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        } else {
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->latest()
                ->get();
        }
        
    
            $pdf = PDF::loadView('reports.pdf.generate-inventory-pdf',compact('data'))->setPaper('A4','landscape');
        
            return $pdf->download('Inventory_List_Report_' . date('Ymd') . '_' . date('His').'.pdf');
    
    }

    public function export_inventory_list(Request $request)
    {
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        $location='';
        $employee='';
        $property_no='';
        $count='';
        $row_count='';
        $filters = [];

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.location_id',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name as fname',
            'employees.last_name as lname',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

        $rows = [];
        $total_amount = 0;
        foreach($data as $d)
        {
            $rows[]=array(
                'Asset Number'=>$d->asset_number,
                'Property Number' => $d->property_number,
                'Item Description' => $d->item_description,
                'Acquisition Cost' =>number_format($d->acquisition_cost,2),
                'Location'=>$d->location_name,
                'Employee' =>  ucwords($d->fname.' '.$d->lname),
                'PO No.' => $d->po_number,
                'Supplier' => $d->supplier_name,
                'Date Acquired'=>$d->date_acquired,
                'Date Added'=>date('Y-m-d',strtotime($d->created_at)),
            );
            if($accountable_employee_id != '')
                $employee=ucwords($d->fname.' '.$d->lname);
            if($location_id != '')
                $location=$d->location_name;
            if($property_number != '')
                $property_no = $d->property_number;

            $total_amount += $d->acquisition_cost;
        }
        if($data->count()==0)
            return redirect(route('report.inventory_list'))->with('error', 'Empty Generated Report.');
        else {
            $count = $data->count();
            $row_count=$count+4;
        }
        $date=date('Y-m-d');
        if( $date_range_pick == ''){
            $from='';
            $to='';
        }


        Excel::create('Inventory_list_report_'.date('Ymd').'_'.date('His'), function($excel) use ($rows,$date,$from,$to,$location,$employee,$property_no,$count,$row_count,$total_amount) {
            $excel->sheet('Inventory_list_sheet', function($sheet) use ($rows,$date,$from,$to,$location,$employee,$property_no,$count,$row_count,$total_amount){

                $sheet->cells('A1', function ($cells) {
                    $cells->setFont(array(
                        'family' => 'arial',
                        'size' => '20',
                        'bold' => true));
                });
                $sheet->mergeCells('A1:J1');
                $sheet->cell('A1', function($cell) {
                    $cell->setValue('INVENTORY LIST REPORT')->setAlignment('center');
                });
                $sheet->setCellValue('A2', 'Date: ' . $date);
                if($employee != '')
                    $sheet->setCellValue('C2', 'Employee: '.$employee);
                if($location != '')
                    $sheet->setCellValue('E2', 'Location: '.$location);
                if($property_no != '')
                    $sheet->setCellValue('G2', 'Property Number: '.$property_no);

                if($from != '' AND $to != '') {
                    $sheet->setCellValue('I2', 'From: ' . $from );
                    $sheet->setCellValue('J2', 'To: ' . $to);
                }

                $sheet->fromArray($rows, null, 'A3', true);
                $sheet->setCellValue('J'.$row_count,'Number of Asset: '.$count);
                $row_count++;
                $sheet->setCellValue('J'.$row_count,'Total Amount: '.number_format($total_amount, 2));

            });

        })->download('xlsx');
    }

    public function get_inventory_list(Request $request){
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
    
        $filters = [];
        
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }
        
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
        if($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }elseif(count($filters)){
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                // ->limit(0)
                ->get();
        }




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
                return '<span class="datatables-acquisition_cost" data-amount="'.$row->acquisition_cost.'">'.number_format($row->acquisition_cost, 2).'</span>';
            })
            ->addIndexColumn()
            ->addColumn('location', function($row){
                return ucfirst($row->location_name);
            })
            ->addIndexColumn()
            ->addColumn('employee', function($row){
                $url_edit   = route('employee.show', ['slug_token'=>$row->slug_token_employee]);
                $name       = ucwords($row->first_name.' '.$row->last_name);
                return '<a data-description="'.$name.'" href="'.$url_edit.'" title="View Employee">'.$name.'</a>';
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
            ->addColumn('date_added', function($row){
                return Utility::get_date_added_format($row->created_at);
            })
            ->rawColumns(['asset_no','property_no','item_description','acquisition_cost','location', 'employee', 'po_no', 'supplier', 'date_acquired','date_added' ])
            ->make(true);

        return $data_tables;
    }

    public function uploaded_scan(){
        return view('reports.uploaded_scan');
    }

    public function get_uploaded_scan(){

    }

    public function print_asset_barcodes(Request $request){
        
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;
        
        $filters = [];
        
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
        }
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
        }
          
        $to_select = array(
            'assets.property_number',
        );
      
        if( $date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick );
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));
            
            $data = Asset::where($filters)
            ->select($to_select)
            ->whereBetween('acquisition_date',[$from,$to])
            ->orderBy('assets.created_at', 'desc')
            // ->limit(10)
            ->get();
        } else {
            $data = Asset::where($filters)
            ->select($to_select)
            ->orderBy('assets.created_at', 'desc')
            // ->limit(10)
            ->get();
        }

        return view('reports/print.print-assets-barcode', compact('data'));
    }


    public function print_asset_report(Request $request){
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }

        $filters = [];
        $filter  = array(
            'Employee'      => '', 
            'Location'      => '', 
            'PropertyNumber'=> '', 
            'From'          => '',  
            'To'            => ''
        );

        $to_select_filter_employee  = array('first_name', 'last_name');
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
            $get_employee = Employee::select($to_select_filter_employee)
            ->where('id', $accountable_employee_id)
            ->first();
            $filter['Employee'] = ucwords($get_employee->first_name.' '.$get_employee->last_name);
        }
        
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
            $get_location = Location::select('branch_name as location')
            ->where('id', $location_id)
            ->first();
            $filter['Location'] = ucfirst($get_location->location);
        }
        if($property_number != ''){
            $filters['assets.property_number']  = $property_number;
            $filter['PropertyNumber']           = $property_number;
        }
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        
        return view('reports/print.print-assets-report', compact('data', 'filter'));
    }

    public function print_employee_ledger_report(Request $request){
        
        $accountable_employee_id = $request->employee_id;
        $employee_no             = $request->employee_no;
        $date_range_pick         = $request->date_range_pick;
        $filters = [];
        $filter  = array(
            'Employee'          => '', 
            'EmployeeNumber'    => '', 
            'Department'        => '', 
            'From'              => '',  
            'To'                => ''
        );
        $to_select_filter_employee  = array(
            'employees.first_name', 
            'employees.last_name', 
            'departments.name as department_name', 
        );
        $modelRole               = UserRole::where('user_id', auth()->user()->id)->first();

        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }else if($employee_no != ''){
            $get_employee = Employee::select($to_select_filter_employee)
            ->where('employees.employee_no', $employee_no)
            ->join('departments', 'departments.id', 'employees.department_id')
            ->first();
            $filters['assets.accountable_employee_id'] = $get_employee->id;
            $filter['Employee']     = ucwords($get_employee->first_name.' '.$get_employee->last_name);
            $filter['Department']   = ucfirst($get_employee->department_name);
        }

        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
            $get_employee = Employee::select($to_select_filter_employee)
            ->where('employees.id', $accountable_employee_id)
            ->join('departments', 'departments.id', 'employees.department_id')
            ->first();
            $filter['Employee'] = ucwords($get_employee->first_name.' '.$get_employee->last_name);
            $filter['Department']   = ucfirst($get_employee->department_name);
        }
        
        
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }

        $to_select = array(
            'assets.slug_token as slug_token',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.asset_number',
            'assets.mr_number',
            'assets.created_at',
            'locations.branch_name as location_name',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
            ->select($to_select)
            ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();   
        }
        else{
            $data = Asset::where($filters)
            ->select($to_select)
            ->join('locations', 'locations.id', '=', 'assets.location_id')
            ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
            ->orderBy('assets.created_at', 'desc')
            ->get();
        }

        return view('reports/print.print-employee-ledger-report', compact('data', 'filter'));
    }

    public function print_daily_inventory_report(Request $request){
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }

        $filters = [];
        $filter  = array(
            'Employee'      => '', 
            'Location'      => '', 
            'PropertyNumber'=> '', 
            'From'          => '',  
            'To'            => ''
        );

        $to_select_filter_employee  = array('first_name', 'last_name');
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
            $get_employee = Employee::select($to_select_filter_employee)
            ->where('id', $accountable_employee_id)
            ->first();
            $filter['Employee'] = ucwords($get_employee->first_name.' '.$get_employee->last_name);
        }
        
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
            $get_location = Location::select('branch_name as location')
            ->where('id', $location_id)
            ->first();
            $filter['Location'] = ucfirst($get_location->location);
        }
        if($property_number != ''){
            $filters['assets.property_number']  = $property_number;
            $filter['PropertyNumber']           = $property_number;
        }
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as original_location_name',
            'cl.branch_name as current_location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('uploaded_data_details as udd', 'udd.barcode', '=', 'assets.property_number')
                ->leftJoin('locations as cl', 'cl.id', '=', 'udd.location_id')
                ->whereRaw("udd.created_at >= ? AND udd.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->orderBy('assets.created_at', 'desc')
                ->groupBy('udd.barcode')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->join('uploaded_data_details as udd', 'udd.barcode', '=', 'assets.property_number')
                ->leftJoin('locations as cl', 'cl.id', '=', 'udd.location_id')
                ->orderBy('assets.created_at', 'desc')
                ->groupBy('udd.barcode')
                ->get();
        }
        return view('reports/print.print-daily-inventory-report', compact('data', 'filter'));
    }

    public function print_office_report(Request $request){
        $location_id             = $request->location_id;
        $department_id           = $request->department_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        $filter  = array(
            'Department'    => '', 
            'Location'      => '', 
            'PropertyNumber'=> '', 
            'From'          => '',  
            'To'            => ''
        );

        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
            $get_location = Location::select('branch_name as location')
            ->where('id', $location_id)
            ->first();
            $filter['Location'] = ucfirst($get_location->location);
        }
        if($department_id != ''){
            $filters['assets.department_id'] = $department_id;
            $get_department = Department::select('name as department_name')
            ->where('id', $department_id)
            ->first();
            $filter['Department'] = ucfirst($get_department->department_name);
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
            $filter['PropertyNumber']           = $property_number;
        }
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }


        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

        return view('reports/print.print-office-report', compact('data', 'filter'));
    }

    public function print_category_report(Request $request){
        $category_id             = $request->category_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        $filter  = array(
            'Category'      => '', 
            'Location'      => '', 
            'PropertyNumber'=> '', 
            'From'          => '',  
            'To'            => ''
        );

        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
            $get_location = Location::select('branch_name as location')
            ->where('id', $location_id)
            ->first();
            $filter['Location'] = ucfirst($get_location->location);
        }
        if($category_id != ''){
            $filters['assets.category_id'] = $category_id;
            $get_category = Category::select('name as category_name')
            ->where('id', $category_id)
            ->first();
            $filter['Category'] = ucfirst($get_category->category_name);
        }
        if($property_number != ''){
            $filters['assets.property_number'] = $property_number;
            $filter['PropertyNumber']           = $property_number;
        }
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );


        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereBetween('assets.created_at',[$from,$to])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }

        return view('reports/print.print-category-report', compact('data', 'filter'));
    }

    public function print_unlocated_items_report(Request $request){
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $filters = [];
        $filter  = array(
            'Location'      => '', 
            'PropertyNumber'=> '', 
            'From'          => '',  
            'To'            => ''
        );

        if($location_id != ''){
            $filters['a.location_id'] = $location_id;
            $get_location = Location::select('branch_name as location')
            ->where('id', $location_id)
            ->first();
            $filter['Location'] = ucfirst($get_location->location);
        }
        if($property_number != ''){
            $filters['a.property_number'] = $property_number;
            $filter['PropertyNumber']           = $property_number;
        }
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }

        $to_select = array(
            'a.asset_number',
            'a.property_number',
            'a.item_description',
            'a.acquisition_cost',
            'a.po_number',
            'a.slug_token',
            'a.acquisition_date as date_acquired',
            'a.created_at',
            
            'e.slug_token as slug_token_employee',
            'e.first_name',
            'e.last_name',

            'l.branch_name as location_name',
            's.name as supplier_name',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));            

            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->whereRaw("udd.created_at >= ? AND udd.created_at <= ?",[$from.' 00:00:00',$to.' 23:59:59'])
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->get();

        }
        else if(count($filters)){
            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->get();
        }
        else{

            $get_udd = UploadedDataDetails::from('uploaded_data_details as udd')
            ->select(array('udd.barcode'))
            ->groupBy('udd.barcode')
            ->get();

            $p_array = array();
            foreach($get_udd as $row){
                array_push($p_array, $row->barcode);
            }

            $data = Asset::from('assets as a')
            ->select($to_select)
            ->join('employees as e', 'e.id', '=', 'a.accountable_employee_id')
            ->leftJoin('locations as l', 'l.id', '=', 'a.location_id')
            ->leftJoin('suppliers as s', 's.id', '=', 'a.supplier_id')
            ->where($filters)
            ->whereNotIn('a.property_number', $p_array)
            ->orderBy('a.created_at', 'desc')
            ->limit(0)
            ->get();
        }

        return view('reports/print.print-unlocated-items-report', compact('data', 'filter'));
    }

    public function print_inventory_list_report(Request $request){
        $accountable_employee_id = $request->employee_id;
        $location_id             = $request->location_id;
        $property_number         = $request->property_number;
        $date_range_pick         = $request->date_range_pick;

        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        if( $modelRole->role_id != '1'){ //select assets for this employees only
            $accountable_employee_id = Utility::current_employee_id();
        }

        $filters = [];
        $filter  = array(
            'Employee'      => '', 
            'Location'      => '', 
            'PropertyNumber'=> '', 
            'From'          => '',  
            'To'            => ''
        );

        $to_select_filter_employee  = array('first_name', 'last_name');
        if($accountable_employee_id != ''){
            $filters['assets.accountable_employee_id'] = $accountable_employee_id;
            $get_employee = Employee::select($to_select_filter_employee)
            ->where('id', $accountable_employee_id)
            ->first();
            $filter['Employee'] = ucwords($get_employee->first_name.' '.$get_employee->last_name);
        }
        
        if($location_id != ''){
            $filters['assets.location_id'] = $location_id;
            $get_location = Location::select('branch_name as location')
            ->where('id', $location_id)
            ->first();
            $filter['Location'] = ucfirst($get_location->location);
        }
        if($property_number != ''){
            $filters['assets.property_number']  = $property_number;
            $filter['PropertyNumber']           = $property_number;
        }
        if($date_range_pick != ''){
            $date_array     = explode(" - ",$date_range_pick);
            $from           = date('Y-m-d',strtotime($date_array[0]));
            $to             = date('Y-m-d',strtotime($date_array[1]));
            $filter['From'] = $from;
            $filter['To']   = $to;
        }

        $to_select = array(
            'assets.id as asset_id',
            'assets.asset_number',
            'assets.property_number',
            'assets.item_description',
            'assets.acquisition_cost',
            'assets.po_number',
            'assets.slug_token',
            'assets.acquisition_date as date_acquired',
            'assets.created_at',
            'employees.slug_token as slug_token_employee',
            'employees.id as employee_id',
            'employees.first_name',
            'employees.last_name',
            'locations.branch_name as location_name',
            'suppliers.name as supplier_name',
        );

        if ($date_range_pick != ''){
            $date_array  = explode(" - ",$date_range_pick);
            $from = date('Y-m-d',strtotime($date_array[0]));
            $to = date('Y-m-d',strtotime($date_array[1]));

            $data = Asset::where($filters)
                ->select($to_select)
                ->whereRaw("assets.created_at >= ? AND assets.created_at <=?",[$from.' 00:00:00',$to.' 23:59:59'])
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        else{
            $data = Asset::where($filters)
                ->select($to_select)
                ->join('locations', 'locations.id', '=', 'assets.location_id')
                ->join('suppliers', 'suppliers.id', '=', 'assets.supplier_id')
                ->join('employees', 'employees.id', '=', 'assets.accountable_employee_id')
                ->orderBy('assets.created_at', 'desc')
                ->get();
        }
        
        return view('reports/print.print-inventory-list-report', compact('data', 'filter'));
    }
}
