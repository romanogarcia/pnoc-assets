<?php

namespace App\Http\Controllers;
use App\Asset;
use Utility;
use Illuminate\Http\Request;
use App\UploadedDataDetails;
class UploadedDataDetailsController extends Controller
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
        $validatedData = $this->validate($request, [
            'uploaded_data_id'  => 'nullable',
            'barcode'           => 'required',
            'location'          => 'required',
            'asset_id'          => 'nullable',

        ]);

        $get_asset  = Asset::where('property_number', $request->barcode)->first();
        $location_id            = Utility::validate_location($request->location);

        $barcode    = new UploadedDataDetails();

        // Check if exist the input
        if ($get_asset != null){
            $barcode->asset_id  = $get_asset->id;
            $flash              = array(
                                    'status'    =>'found', 
                                    'asset_id'  => $get_asset->id, 
                                    'barcode'   => $request->barcode,
                                );
        }else{
            $flash              = array(
                                    'status'    => 'new', 
                                    'asset_id'  => 0, 
                                    'barcode'   => $request->barcode,
                                );
        }

        $barcode->location_id   = $location_id;
        $barcode->barcode       = $request->barcode;
        $barcode->uploaded_by   = auth()->user()->id;
        $is_saved               = $barcode->save();
        if($is_saved)
            \Session::flash('scanned_barcode_entry', $flash);
        \Session::flash('current_location', $location_id);

        return redirect(route('uploaded_data.scan_barcode'))->with('success', 'Successfully Scanned');
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
}
