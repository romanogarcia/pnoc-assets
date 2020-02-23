@extends('layouts.master')
@section('title', 'Barcode Entry Form - PNOC')
@section('customcss')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/jquery-datepicker/jquery-daterangepicker.css')}}" />
@endsection

@section('content')
   <div class="content-wrapper">
   
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">Barcode Entry</div>
               <div class="card-body">

                <form id="form-scan_barcode" class="forms-sample" method="POST" action="{{ route('uploaded_data_details.store') }}">
                    @csrf
                    <div id="search-form_container">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="location"><span class="text-danger">*</span> Current Location</label>
                                    <div class="input-group">
                                        <select class="form-control @error('location') is-invalid @enderror" name="location" id="location" >
                                        <option value="">-Location-</option>
                                        @foreach($locations as $location)
                                            <option @if($location->id == old('location') || $current_location == $location->id) selected @endif value="{{$location->id}}">{{ucfirst($location->branch_name)}}</option>
                                        @endforeach
                                        </select>
                                        @error('location')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="property_no"><span class="text-danger">*</span> Barcode No.</label>
                                    <input type="text" autofocus class="form-control @error('barcode') is-invalid @enderror" name="barcode" id="barcode" placeholder="Scan Barcode..." autocomplete="off">
                                    @error('barcode')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <!-- <div class="form-group text-right" style="visibility: hidden;">
                                    <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Submit</button>
                                </div> -->
                            </div>
                            <div class="col-md-8 offset-md-1">
                                @if($flash_data)
                                    <br><br>
                                    @if($get_flash['status'] == 'new')
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <b>STATUS:</b> <label class="badge badge-primary">NEW</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <b>BARCODE/PROPERTY NO.:</b> {{$get_flash['barcode']}}
                                            </div>
                                        </div>
                                    @else
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <b>STATUS:</b> <label class="badge badge-success">FOUND</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <b>BARCODE/PROPERTY NO.:</b> <a href="{{route('asset.show', ['slug_token'=>$flash_data->asset_slug_token])}}" target="_blank" title="Edit Asset Information">{{$get_flash['barcode']}}</a> 
                                            </div>
                                        </div>
                                            
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>CATEGORY</b></div>
                                                    <div>{{ucfirst($flash_data->category_name)}}</div>                                        
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>DEPARTMENT</b></div>
                                                    <div>{{ucfirst($flash_data->department_name)}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>SUPPLIER</b></div>
                                                    <div>{{ucfirst($flash_data->supplier_name)}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>LOCATION</b></div>
                                                    <div>{{ucfirst($flash_data->location_name)}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>ACCOUNTABLE EMPLOYEE</b></div>
                                                    <div><a href="{{route('employee.show', ['slug_token'=>$flash_data->accountable_employee_slug_token])}}" title="View Employee" target="_blank">{{ucwords($flash_data->employee_first_name.' '.$flash_data->employee_last_name)}}</a></div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>ITEM DESCRIPTION</b></div>
                                                    <div>{{ucfirst($flash_data->item_description)}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>CONDITION</b></div>
                                                    <div>{{ucfirst($flash_data->condition)}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>ACCOUNTING TAG</b></div>
                                                    <div>{{$flash_data->accounting_tag}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>PROPERTY NUMBER</b></div>
                                                    <div>{{$flash_data->property_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>SERIAL NUMBER</b></div>
                                                    <div>{{$flash_data->serial_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>ACQUISITION COST</b></div>
                                                    <div>{{number_format($flash_data->acquisition_cost, 2)}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>ASSET NUMBER</b></div>
                                                    <div>{{$flash_data->asset_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>PO NUMBER</b></div>
                                                    <div>{{$flash_data->po_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>MR NUMBER</b></div>
                                                    <div>{{$flash_data->mr_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>WARRANTY</b></div>
                                                    <div>{{$flash_data->warranty}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>REPORT OF WASTE</b></div>
                                                    <div>{{$flash_data->report_of_waste_material}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>DISPOSAL NUMBER</b></div>
                                                    <div>{{$flash_data->disposal_number}}</div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6 col-xs-6">
                                                <div class="form-group">
                                                    <div><b>DATE ACQUIRED</b></div>
                                                    <div>{!!Utility::get_date_format($flash_data->date_acquired)!!}</div>
                                                </div>
                                            </div>
                                        </div>

                                        @if(strlen($flash_data->used_by_history) > 5)
                                            <hr>
                                            <b>LAST ACCOUNTABLE EMPLOYEES</b>
                                            @foreach(json_decode($flash_data->used_by_history, true) as $history)
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <p class="clearfix">
                                                            <span class="float-left text-muted" style="padding-top: 10px;">
                                                                {{ucwords($history['employee_name'])}}
                                                            </span>
                                                            <span class="float-right text-muted">
                                                            <small>
                                                                FR: {{date('m-d-Y', strtotime($history['from']))}} 
                                                                <br> 
                                                                TO: {{date('m-d-Y', strtotime($history['to']))}}
                                                            </small>
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif

                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
               
               </div>
            </div>
         </div>
      </div>
   </div>


@endsection

@section('customjs')

<script type="text/javascript" src="{{asset('plugins/select2/custom-option.js')}}"></script>
<script type="text/javascript">
    load_select2();
    function load_select2(){
        $('#location').select2(select2_custom_option_dynamic());
        load_select2_custom_layout();
    }

    $("#form-scan_barcode").on('submit', function(){
        $(this).find('input').attr('readonly', true);
    });
  </script>
@endsection


