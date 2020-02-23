<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\UploadedData;
use App\UploadedDataDetails;
use App\Asset;
use App\Employee;
use App\Location;
use Utility;
use DataTables;
use DNS1D;
use DNS2D;

class UploadedDataController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->current_user_id = auth()->user()->id;

            if(!Utility::get_barcode_module_access()){
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
    public function index()
    {
        // 
    }

    public function upload_file(){
        $locations = Location::orderBy('branch_name', 'asc')->get();
        return view('barcode/uploaded_data.upload_file', compact('locations'));
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
            'description'   => 'nullable',
            'location'      => 'required',
            'file'          => 'required|mimes:txt',
        ]);


        $description    = $request->description;

        $uploaded_file          = $request->file('file');
        $extension              = $uploaded_file->getClientOriginalExtension();
        $original_name          = pathinfo($uploaded_file->getClientOriginalName(), PATHINFO_FILENAME);
        $generated_file_name    = Utility::generate_unique_token();

        $file                   = $generated_file_name.'.'.$extension;
        $path                   = Utility::create_new_upload_dir('uploaded_data'); // public/uploads/uploaded_data/
        $is_uploaded            = $uploaded_file->move($path, $file);
            
        $uploaded_by            = auth()->user()->id;
        $slug_token             = Utility::generate_unique_token();

        $uploaded_data                   = new UploadedData();
        $uploaded_data->file             = $file;
        $uploaded_data->file_extension   = $extension;
        $uploaded_data->file_name        = $original_name;
        $uploaded_data->slug_token       = $slug_token;
        $uploaded_data->description      = $description;
        $uploaded_data->uploaded_by      = $uploaded_by;
        $is_saved                        = $uploaded_data->save();

        if($is_saved){
            $file_contents      = Utility::get_file_contents($path.$file);
            $file_contents_list = explode("\r\n", $file_contents);
            
            $location_id        = Utility::validate_location($request->location);

            foreach($file_contents_list as $barcode){
                $filter                     = [];
                $filter['barcode']          = $barcode;
                $filter['uploaded_data_id'] = $uploaded_data->id;
                $udd_is_exist = UploadedDataDetails::where($filter);
                if($barcode != '' && $udd_is_exist->count() <= 0){
                    $uploaded_data_details                          = new UploadedDataDetails();
                    $uploaded_data_details->uploaded_data_id        = $uploaded_data->id;
                    $uploaded_data_details->barcode                 = $barcode;
                    $uploaded_data_details->location_id             = $location_id;
                    
                    $get_asset                                      = Asset::where('property_number', $barcode)->first('id');
                    if($get_asset)
                        $uploaded_data_details->asset_id            = $get_asset->id;
                    
                    $uploaded_data_details->save();
                }

            }

        }

        return redirect()->back()->with('success', 'Successfully Uploaded.');
    }

    public function get_uploaded_file(Request $request){
        $to_select = array(
            'ud.file_name',
            'ud.file',
            'ud.file_extension',
            'ud.description',
            'ud.created_at as date_uploaded',
            'ud.slug_token as uploaded_data_slug_token',
            'em.first_name',
            'em.last_name',
            'em.slug_token as employee_slug_token',
            'u.slug_token as user_slug_token',
            'u.username',
        );

        $data = UploadedData::from('uploaded_data as ud')
        ->select($to_select)
        ->leftJoin('users as u', 'u.id', '=', 'ud.uploaded_by')
        ->leftJoin('employees as em', 'em.user_id', '=', 'u.id')
        ->orderBy('ud.created_at', 'desc')
        ->get();

        $data_tables = Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('file', function($row){
                $original_file = $row->file_name.'.'.$row->file_extension;
                return '<a data-description="'.$original_file.'" href="'.asset('uploads/uploaded_data/'.$row->file).'" target="_blank" download="'.$original_file.'" title="Download"><i class="mdi mdi-download"></i> '.$original_file.'</a>';
            })
            ->addIndexColumn()
            ->addColumn('file_size', function($row){
                $file_path  = public_path('uploads/uploaded_data/'.$row->file);
                $file_size  = filesize($file_path);
                $response   = Utility::get_formatted_file_size($file_size);
                return $response;
            })
            ->addIndexColumn()
            ->addColumn('description', function($row){
                return $row->description;
            })
            ->addIndexColumn()
            ->addColumn('uploaded_by', function($row){
                if($row->first_name){
                    $name       = ucwords($row->first_name).' '.ucwords($row->last_name);
                    $url        = route('employee.show', ['slug_token'=>$row->employee_slug_token]);
                }else{
                    $name       = $row->username;
                    $url        = route('user.show', ['slug_token'=>$row->user_slug_token]);
                }

                $response = '<a data-description="'.$name.'" href="'.$url.'" target="_blank" title="View Profile">'.$name.'</a>';
                return $response;
            })
            ->addIndexColumn()
            ->addColumn('date_uploaded', function($row){
                return Utility::get_date_added_format($row->date_uploaded);
            })
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $original_file = $row->file_name.'.'.$row->file_extension;
                $response = '<div class="text-right">';
                $response .= '<button class="btn btn-xs btn-danger btn-delete_data" data-slug_token="'.$row->uploaded_data_slug_token.'" data-file="'.$original_file.'" type="button" title="Delete"><i class="mdi mdi-delete"></i></button>';
                $response .= '</div>';
                return $response;
            })
            ->rawColumns(['file','file_size','description','uploaded_by','date_uploaded','action'])
            ->make(true);
        
        return $data_tables;
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
    public function destroy($slug_token)
    {
        // dd($slug_token);
        $data = UploadedData::where('slug_token', $slug_token)->firstOrFail();
        
        $file = $data->file;
        $path = public_path('uploads/uploaded_data/');
        
        if($file != ''){
            if(file_exists($path.$file)){
                unlink($path.$file);
            }
        }
        $data->delete();

        return redirect(route('uploaded_data.upload_file'))->with('success', 'Successfully deleted.');
    }

    public function scan_barcode(){
        $employees          = Employee::orderBy('first_name', 'asc')->orderBy('last_name', 'asc')->get();
        $locations          = Location::orderBy('branch_name', 'asc')->get();
        $current_location   = null;

        if (\Session::has('current_location'))
            $current_location     = \Session::get('current_location');

        if (\Session::has('scanned_barcode_entry')){
            $get_flash     = \Session::get('scanned_barcode_entry');

            if($get_flash['status'] == 'found'){
                $to_select = array(
                    'assets.*',
                    'assets.id as asset_id',
                    'assets.created_at as asset_created_at',
                    'assets.updated_at as asset_updated_at',
                    'assets.slug_token as asset_slug_token',
                    'assets.acquisition_date as date_acquired',
                    'assets.condition as condition',
                    'assets.accounting_tag as accounting_tag',
        
                    'ea.first_name as employee_first_name',
                    'ea.last_name as employee_last_name',
                    'ea.slug_token as accountable_employee_slug_token',
        
                    'l.branch_name as location_name',
                    's.name as supplier_name',
                    'd.name as department_name',
                    'c.name as category_name',
                );
        
                $flash_data = Asset::where('assets.id', $get_flash['asset_id'])
                ->select($to_select)
                ->join('employees as ea', 'ea.id', '=', 'assets.accountable_employee_id')
                ->join('locations as l', 'l.id', '=', 'assets.location_id')
                ->join('suppliers as s', 's.id', '=', 'assets.supplier_id')
                ->join('departments as d', 'd.id', '=', 'assets.department_id')
                ->join('categories as c', 'c.id', '=', 'assets.category_id')
                ->firstOrFail();
            }else{
                $flash_data = true;
            }
            

        }else{
            $flash_data     = false;
            $get_flash      = false;
        }

        return view('barcode/uploaded_data.scan_barcode', compact('employees', 'locations', 'flash_data', 'get_flash', 'current_location'));
    }

    public function scanned_barcode_list(){
        return view('barcode/uploaded_data.scanned_barcode_list');
    }

    public function get_scanned_barcode_list(Request $request){

        $to_select = array(
            'udd.id as r_id',
            'udd.created_at as date_scan',
            'udd.barcode',
            'udd.asset_id',

            'ai.slug_token as asset_slug_token',
            
            'ud.file',
            'ud.file_extension',
            'ud.file_name',
            
            'u_dd.slug_token as udd_slug_token',
            'u_dd.username as udd_username',

            'u_d.slug_token as ud_slug_token',
            'u_d.username as ud_username',
        );

        $data = UploadedDataDetails::from('uploaded_data_details as udd')
        ->select($to_select)
        ->leftJoin('users as u_dd', 'u_dd.id', '=', 'udd.uploaded_by')
        ->leftJoin('assets as ai', 'ai.id', '=', 'udd.asset_id')
        ->leftJoin('uploaded_data as ud', 'ud.id', '=', 'udd.uploaded_data_id')
        ->leftJoin('users as u_d', 'u_d.id', '=', 'ud.uploaded_by')
        ->orderBy('udd.created_at', 'desc')
        ->get();

        $data_tables = Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('barcode', function($row){
                if($row->asset_id){
                    $url_edit = route('asset.show', ['slug_token'=>$row->asset_slug_token]);
                    $response = '<a href="'.$url_edit.'" title="Asset Details" target="_blank" data-id="'.$row->r_id.'">'.$row->barcode.'</a>';
                }
                else{
                    $response = $row->barcode;
                }

                return $response;
            })
            ->addIndexColumn()
            ->addColumn('status', function($row){
                if($row->asset_id)
                    $response = '<label data-description="Found" class="badge badge-success">Found</label>';
                else
                    $response = '<label data-description="New" class="badge badge-primary">New</label>';

                return $response;
            })
            ->addIndexColumn()
            ->addColumn('added_by', function($row){
                if($row->udd_slug_token){
                    $response = '<a data-description="'.$row->udd_username.'" title="View Profile" href="'.route('user.show', ['slug_token'=>$row->udd_slug_token]).'" target="_blank">'.$row->udd_username.'</a> <br><small><i>Source: Barcode entry</i></small>';
                }
                else{
                    $file           = $row->file;
                    $file_name      = $row->file_name;
                    $file_extension = $row->file_extension;
                    $original_file  = $file_name.'.'.$file_extension;

                    $response = '<a data-description="'.$row->ud_username.'" title="View Profile" href="'.route('user.show', ['slug_token'=>$row->ud_slug_token]).'" target="_blank">'.$row->ud_username.'</a> <br>';
                    $response .= '<small><i>Source: <a href="'.asset('uploads/uploaded_data/'.$file).'" download="'.$original_file.'" title="Download">'.$original_file.'</a></i></small>';
                }

                return $response;
            })
            ->addIndexColumn()
            ->addColumn('date_scan', function($row){
                return Utility::get_date_added_format($row->date_scan);
            })
            ->rawColumns(['barcode','added_by','status','date_scan'])
            ->make(true);
        
        return $data_tables;
    }

    public function print_custom_barcode(){
        return view('barcode.print_custom_barcode');
    }

    public function print_preview_custom_barcode_ajax(Request $request){
        $rules = array(
            'property_number' => 'required',
        );
        $errors = $rules;

        $validator = Validator::make($request->all(), $rules);
        $messages = $validator->getMessageBag()->toArray();
        

        foreach($errors as $error_key => $error_value){
            if(!array_key_exists($error_key, $messages))
                $errors[$error_key] = '';
            else
                $errors[$error_key] = $messages[$error_key];
        }
        

        $response = array(
            'success'           => false,
            'errors'            => $errors,
            'barcode'           => '',
            'property_number'   => $request->property_number,
        );

        if(!$validator->fails()){
            $response['success'] = true;
            $response['barcode'] = DNS1D::getBarcodePNG($request->property_number, 'C128');
        }
        return $response;
    }

    public function print_preview_custom_barcode($property_numbers='', $layout_type=''){
        $property_numbers = explode(',', $property_numbers);
        return view ('barcode/print.print_preview_custom_barcode', compact('property_numbers', 'layout_type'));
    }
}
