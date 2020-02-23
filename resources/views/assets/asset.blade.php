@extends('layouts.master')
@section('title', 'Asset Information - PNOC')
@section('customcss')
  <style> </style>
@endsection

@section('content')
    <?php
        $last_user_id='';
    ?>
   <div class="content-wrapper">
   
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">Asset Information - {{$data->asset_number}}</div>
               <div class="card-body">
                  <!-- Row start -->
                  <div class="row">
                    <!-- Column left start -->
                    <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                        <h4 class="text-center">Accountable Employee Details</h4>
                        <hr>
                        <div class="border-bottom text-center pb-4">
                          <a href="{{route('employee.show', ['slug_token'=>$data->accountable_employee_slug_token])}}" title="View Employee">
                            <img src="{{Utility::get_employee_photo($data->employee_photo)}}" alt="profile" class="img-lg rounded-circle mb-3">
                          </a>
                          <div class="mb-3">
                            <h3>{{ucwords($data->employee_first_name.' '.$data->employee_last_name)}}</h3>
                            @if($data->employee_address)
                            <div class="d-flex align-items-center justify-content-center">
                              <h5 class="mb-0 mr-2 text-muted">{{ucfirst($data->employee_address)}}</h5>
                            </div>
                            @endif
                          </div>
                        </div>
                        <div class="py-4">
                          <p class="clearfix">
                            <span class="float-left">
                              Is Active
                            </span>
                            <span class="float-right text-muted">
                              @if($data->employee_is_active)
                                  <label class="badge badge-success">Yes</label>
                              @else
                                  <label class="badge badge-danger">No</label>
                              @endif
                            </span>
                          </p>
                          @if($data->employee_phone)
                          <p class="clearfix">
                            <span class="float-left">
                              Phone
                            </span>
                            <span class="float-right text-muted">
                              {{$data->employee_phone}}
                            </span>
                          </p>
                          @endif
                          <p class="clearfix">
                            <span class="float-left">
                              Email
                            </span>
                            <span class="float-right text-muted">
                              {{$data->employee_email}}
                            </span>
                          </p>
                          <p class="clearfix">
                            <span class="float-left">
                              Department
                            </span>
                            <span class="float-right text-muted">
                              {{ucfirst($data->employee_department_name)}}
                            </span>
                          </p>
                          <!-- <p class="clearfix">
                            <span class="float-left">
                              Location
                            </span>
                            <span class="float-right text-muted">
                              {{ucfirst($data->employee_location_name)}}
                            </span>
                          </p> -->
                          <p class="clearfix">
                            <span class="float-left">
                              Member Since
                            </span>
                            <span class="float-right text-muted">
                              {{date("F Y", strtotime($data->employee_created_at))}}
                            </span>
                          </p>
                        </div>
                        @if(Utility::get_current_role() == '1')
                        <button class="btn btn-primary btn-block mb-2" type="button" onclick="window.location='{{route('employee.show', ['slug_token'=>$data->accountable_employee_slug_token])}}'">View Profile</button>
                        @endif
                        <hr>
                        <div class="text-center"><b>Added by</b></div>
                        <div class="py-4">
                          <p class="clearfix">
                            <span class="float-left">
                              Name
                            </span>
                            <span class="float-right text-muted">
                              @if($data->added_by_first_name)
                                <a href="{{route('employee.show', ['slug_token'=>$data->added_by_slug_token])}}">{{ucwords($data->added_by_first_name.' '.$data->added_by_last_name)}}</a>
                              @else
                                <a href="{{route('user.show', ['slug_token'=>$data->user_slug_token])}}">{{$data->username}}</a>
                              @endif
                            </span>
                          </p>
                          <p class="clearfix">
                            <span class="float-left">
                              Date Added
                            </span>
                            <span class="float-right text-muted">
                              {{date("F d, Y",strtotime($data->asset_created_at))}}
                            </span>
                          </p>
                          @if($data->asset_updated_at)
                          <p class="clearfix">
                            <span class="float-left">
                              Last Updated
                            </span>
                            <span class="float-right text-muted">
                              {{date("F d, Y",strtotime($data->asset_updated_at))}}
                            </span>
                          </p>
                          @endif
                        </div>

                        @if(strlen($data->used_by_history) > 5)
                          <hr>
                          <div class="text-center"><b>Last Accountable Employees</b></div>
                          <div class="py-4">

                            <p class="clearfix">
                              <span class="float-left text-muted">
                                <b>NAME</b>
                              </span>
                              <span class="float-right text-muted">
                                <b>DATE ISSUE</b>
                              </span>
                            </p>
                            @foreach(json_decode($data->used_by_history, true) as $history)
                              <p class="clearfix">
                                <span class="float-left text-muted" style="padding-top: 10px;">
                                  <span class="text-danger btn-remove-history" data-history_key="{{$history['key']}}" data-toggle="modal" data-target="#modal-remove_history"style="cursor: pointer;" title="Remove"><i class="mdi mdi-close"></i></span> {{ucwords($history['employee_name'])}}
                                </span>
                                <span class="float-right text-muted">
                                  <small>
                                    FR: {{date('m-d-Y', strtotime($history['from']))}} 
                                    <br> 
                                    TO: {{date('m-d-Y', strtotime($history['to']))}}
                                  </small>
                                </span>
                              </p>
                            @endforeach

                          </div>
                        @endif
                      </div>
                      <!-- /- Column left end -->

                      <!-- Column right start -->
                      <div class="col-xs-12 col-sm-7 col-md-8 col-lg-9">
                        <div class="row">
                          <div class="col-lg-12">
                            <div class="card">
                              <div class="card-header">
                                Asset Details 
                                @if($data->property_number != '' && $data->property_number != ' ')
                                  <div class="float-right">
                                    <button type="button" onclick="window.open('{{route('asset.get_print_barcode', ['slug_token' => $data->asset_slug_token])}}');" class="btn btn-primary btn-sm" title="Print Barcode">Print Barcode <i class="mdi mdi-printer btn-icon-append"></i></button>
                                  <!--  <a style="position: relative; z-index: 999;" href="javascript:void(0);" class="btn btn-danger btn-icon-text btn-pdf-download btn-sm">
                                        Export to PDF <i class="mdi mdi mdi-file-pdf btn-icon-append"></i>
                                      </a> -->
                                  </div>
                                @endif
                              </div>
                              @if($data->property_number != '' && $data->property_number != ' ')
                              <div class="card-body text-center">
                                <p><b> BARCODE</b></p>
                                <img src="data:image/png;base64,{!!DNS1D::getBarcodePNG($data->property_number, 'C128', 3, 33)!!}" alt="Barcode" height="38" width="180">
                                <div style="margin-top: 5px;">
                                  {{$data->property_number}}
                                </div>
                              </div>
                              @endif
                            </div>
                          </div>
                        </div>
                        <br>
                        <form class="forms-sample" method="POST" action="{{route('asset.update', ['slug_token'=>$data->asset_slug_token])}}">
                        @method('PUT')
                        @csrf
                          <div class="row">
                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="form-group">
                                    <label for="category"><span class="text-danger">*</span> Category</label>
                                    <div class="input-group">
                                        <select class="form-control @error('category') is-invalid @enderror" name="category" id="category">
                                          <option value="">-Category-</option>
                                          @foreach($categories as $category)
                                              <option @if($category->id == old('category') || $category->id == $data->category_id) selected @endif value="{{$category->id}}">{{ucfirst($category->name)}}</option>
                                          @endforeach

                                        </select>
                                        @error('category')
                                          <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                    </div>
                                  </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="form-group">
                                    <label for="department"><span class="text-danger">*</span> Department</label>
                                    <div class="input-group">
                                        <select class="form-control @error('department') is-invalid @enderror" name="department" id="department" >
                                          <option value="">-Department-</option>
                                          @foreach($departments as $department)
                                              <option @if($department->id == old('department') || $department->id == $data->department_id) selected @endif value="{{$department->id}}">{{ucfirst($department->name)}}</option>
                                          @endforeach
                                        </select>
                                        @error('department')
                                          <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                    </div>
                                  </div>

                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="form-group">
                                    <label for="supplier"><span class="text-danger">*</span> Supplier</label>
                                    <div class="input-group">
                                        <select class="form-control @error('supplier') is-invalid @enderror" name="supplier" id="supplier">
                                          <option value="">-Supplier-</option>
                                          @foreach($suppliers as $supplier)
                                              <option @if($supplier->id == old('supplier') || $supplier->id == $data->supplier_id) selected @endif value="{{$supplier->id}}">{{ucfirst($supplier->name)}}</option>
                                          @endforeach
                                        </select>
                                        @error('supplier')
                                          <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                    </div>
                                  </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="form-group">
                                    <label for="location"><span class="text-danger">*</span> Location</label>
                                    <div class="input-group">
                                        <select class="form-control @error('location') is-invalid @enderror" name="location" id="location">
                                          <option value="">-Location-</option>
                                          @foreach($locations as $location)
                                            <option @if($location->id == old('location') || $location->id == $data->location_id) selected @endif value="{{$location->id}}">{{ucfirst($location->branch_name)}}</option>
                                          @endforeach
                                        </select>
                                        @error('location')
                                          <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                    </div>
                                  </div>
                              </div>

                              

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="form-group">
                                    <label for="employee"><span class="text-danger">*</span> Accountable Employee</label>
                                    <div class="input-group">
                                        <select style="border: 1px solid #aeaeae;" class="form-control @error('employee') is-invalid @enderror" name="employee" id="employee">
                                          <option value="">-Employee-</option>
                                          @foreach($employees as $employee)
                                              <option @if($employee->id == old('employee') || $employee->id == $data->employee_id) selected @endif value="{{$employee->id}}">{{ucfirst($employee->first_name)}} {{ucfirst($employee->last_name)}}</option>
                                          @endforeach
                                        </select>
                                        @error('employee')
                                          <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                          </span>
                                        @enderror
                                    </div>
                                  </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="item_description"><span class="text-danger">*</span>  Item Description</label>
                                  <input type="text" class="form-control @error('item_description') is-invalid @enderror" name="item_description" id="item_description" placeholder="Item Description" value="{{ (old('item_description')) ? old('item_description') : $data->item_description }}">
                                  @error('item_description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="condition">Condition</label>
                                  <input type="text" class="form-control @error('condition') is-invalid @enderror" name="condition" id="condition" placeholder="Condition" value="{{ (old('condition')) ? old('condition') : $data->condition }}">
                                  @error('condition')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <!-- <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="accounting_tag">Accounting Tag</label>
                                  <input type="text" class="form-control @error('accounting_tag') is-invalid @enderror" name="accounting_tag" id="accounting_tag" placeholder="Accounting Tag" value="{{ (old('accounting_tag')) ? old('accounting_tag') : $data->accounting_tag }}">
                                  @error('accounting_tag')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div> -->

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="property_number"><span class="text-danger">*</span>  Property Number</label>
                                  <input type="text" class="form-control @error('property_number') is-invalid @enderror" name="property_number" id="property_number" placeholder="Property Number" value="{{ (old('property_number')) ? old('property_number') : $data->property_number }}">
                                  @error('property_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="serial_number"><span class="text-danger">*</span>  Serial Number</label>
                                  <input type="text" class="form-control @error('serial_number') is-invalid @enderror" name="serial_number" id="serial_number" placeholder="Serial Number" value="{{ (old('serial_number')) ? old('serial_number') : $data->serial_number }}">
                                  @error('serial_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="acquisition_cost"><span class="text-danger">*</span> Acquisition Cost</label>
                                  <input type="text" class="form-control @error('acquisition_cost') is-invalid @enderror" name="acquisition_cost" id="acquisition_cost" placeholder="Acquisition Cost" value="{{ (old('acquisition_cost')) ? old('acquisition_cost') : number_format($data->acquisition_cost,2) }}">
                                  @error('acquisition_cost')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="asset_no"> Asset Number</label>
                                  <input type="text" class="form-control @error('asset_no') is-invalid @enderror" name="asset_no" id="asset_no" placeholder="Asset Number" value="{{ (old('asset_no')) ? old('asset_no') : $data->asset_number }}">
                                  @error('asset_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>
                              
                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="po_no"><span class="text-danger">*</span> PO Number</label>
                                  <input type="text" class="form-control @error('po_no') is-invalid @enderror" name="po_no" id="po_no" placeholder="PO Number" value="{{ (old('po_no')) ? old('po_no') : $data->po_number }}">
                                  @error('po_no')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="mr_number"><span class="text-danger">*</span> MR Number</label>
                                  <input type="text" class="form-control @error('mr_number') is-invalid @enderror" name="mr_number" id="mr_number" placeholder="MR Number" value="{{ (old('mr_number')) ? old('mr_number') : $data->mr_number }}">
                                  @error('mr_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="warranty"> Warranty</label>
                                  <input type="text" class="form-control @error('warranty') is-invalid @enderror" name="warranty" id="warranty" placeholder="Warranty" value="{{ (old('warranty')) ? old('warranty') : $data->warranty }}">
                                  @error('warranty')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="report_of_waste">Report of Waste</label>
                                  <input type="text" class="form-control @error('report_of_waste') is-invalid @enderror" name="report_of_waste" id="report_of_waste" placeholder="Report of Waste" value="{{ (old('report_of_waste')) ? old('report_of_waste') : $data->report_of_waste_material }}">
                                  @error('report_of_waste')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="disposal_number">Disposal Number</label>
                                  <input type="text" class="form-control @error('disposal_number') is-invalid @enderror" name="disposal_number" id="disposal_number" placeholder="Disposal Number" value="{{ (old('disposal_number')) ? old('disposal_number') : $data->disposal_number }}">
                                  @error('disposal_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                <div class="form-group">
                                  <label for="date_acquired"><span class="text-danger">*</span>  Date Acquired</label>
                                  <input type="date" class="form-control @error('date_acquired') is-invalid @enderror" name="date_acquired" id="date_acquired" placeholder="Date Acquired" value="{{ (old('date_acquired')) ? old('date_acquired') : $data->date_acquired }}">
                                  @error('date_acquired')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>

                              <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">
                                  <div class="form-group">
                                      <label for="employee"><span class="text-danger">*</span> Added By</label>
                                      <div class="input-group">
                                          <select style="border: 1px solid #aeaeae;" class="form-control @error('added_by') is-invalid @enderror" name="added_by" id="added_by">
                                              @foreach($users as $user)
                                                  @foreach($employees as $employee)
                                                    @if($user->id==$employee->user_id)
                                                      <option @if($user->id == old('user') || $user->id == $data->user_id) selected @endif value="{{$user->id}}">{{ucwords($employee->first_name.' '.$employee->last_name)}}</option>
                                                      <?php $last_user_id=$employee->user_id;?>
                                                    @endif
                                                  @endforeach
                                                      @if($user->id!=$last_user_id)
                                                  <option @if($user->id == old('user') || $user->id == $data->user_id) selected @endif value="{{$user->id}}">{{ucfirst($user->username)}}</option>
                                                  @endif
                                              @endforeach

                                          </select>
                                          @error('added_by')
                                          <span class="invalid-feedback" role="alert">
                                              <strong>{{ $message }}</strong>
                                          </span>
                                          @enderror
                                      </div>
                                  </div>
                              </div>
                            
                          </div>
                          @if(Utility::get_current_role() == '1')
                          <div class="text-right">
                            <div class="form-group">
                              <div class="">
                                   <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Save</button>
                              </div>
                            </div>
                          </div>
                          @endif

                        </form>

                      </div>
                      <!-- /- Column right end -->

                  </div>
                  <!-- /-Row end -->
               </div>
            </div>
         </div>
      </div>
   </div>

<!-- Delete Modal -->
<div class="modal fade" id="modal-remove_history" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title" id="exampleModalCenterTitle">Delete item</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body" id="modal-delete_alert">
         Are you sure do you want to delete this item?
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
         <form action="" id="form-remove_history" method="POST">
            @method('PUT')
            @csrf
            <input type="hidden" name="history_key" id="input-history_key">
            <button type="submit" class="btn btn-danger" id="form-btn_delete_submit">Delete</button>
         </form>
      </div>
   </div>
   </div>
</div>
@endsection

@section('customjs')
  <script type="text/javascript" src="{{asset('plugins/select2/custom-option.js')}}"></script>
  <script type="text/javascript">
   $(document).ready(function() {

      $.ajaxSetup({
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         }
      });
      @if(Utility::get_current_role() != '1')
        $("input").attr('readonly',true);
        $("select").attr('disabled',true);
      @endif

      load_select2();
      function load_select2(){
        $('#category').select2(select2_custom_option_dynamic());
        $('#location').select2(select2_custom_option_dynamic());
        $('#department').select2(select2_custom_option_dynamic());
        $('#supplier').select2(select2_custom_option_dynamic());
        $('#employee').select2(select2_custom_option_static());
        $('#added_by').select2(select2_custom_option_static());
        load_select2_custom_layout();
      }

   });
   $('.btn-pdf-download').on('click',function(){
	    var query = {
	    	asset_id: $("#asset_no").val(),
	    }
	    var url = "{{URL::to('asset/generate_pdf_asset')}}?" + $.param(query)

	   window.location = url;
	});

  $(".btn-remove-history").on('click', function (){
    var f           = $(this);
    var history_key = f.data('history_key');
    var form        = $("#form-remove_history");
    $("#input-history_key").val(history_key);
    form.attr('action', '{{route("asset.remove_history", ["slug_token" => ""])}}/{{$data->asset_slug_token}}');
  });
  </script>
@endsection