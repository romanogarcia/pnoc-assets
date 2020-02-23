@extends('layouts.master')
@section('title', 'Employee Information - PNOC')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
    
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">Employee Information</div>
               <div class="card-body">
                  <!-- Row start -->
                  <div class="row">
                    <!-- Column left start -->
                    <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                       <br><br>
                        <div class="border-bottom text-center pb-4">
                          <a href="javascript:void(0);" title="Profile Picture" data-toggle="modal" data-target="#form-upload_photo">
                            <img src="{{Utility::get_employee_photo($data->employee_photo)}}" alt="Profile Picture" class="img-lg rounded-circle mb-3">
                          </a>
                          <div class="text-center">
                            <label class="badge badge-primary" data-toggle="modal" data-target="#form-upload_photo" title="Upload New" style="cursor: pointer;"><i class="mdi mdi-upload"></i> Upload New</label>
                          </div>
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
                              @if($data->employee_is_active == 1)
                                <label class="badge badge-success">YES</label>
                              @else
                                <label class="badge badge-danger">NO</label>
                              @endif
                            </span>
                          </p>
                          <p class="clearfix">
                            <span class="float-left">
                              Employee Number
                            </span>
                            <span class="float-right text-muted">
                              {{$data->employee_no}}
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
                            Date Added
                          </span>
                              <span class="float-right text-muted">
                            {{date("F d, Y",strtotime($data->date_added))}}
                          </span>
                          </p>
                            @if($data->date_updated)
                              <p class="clearfix">
                                <span class="float-left">
                                  Last Updated
                                </span>
                                <span class="float-right text-muted">
                                  {{date("F d, Y",strtotime($data->date_updated))}}
                                </span>
                              </p>
                            @endif
                            @if($data->user_slug_token)
                            <button class="btn btn-primary btn-block mb-2" type="button" onclick="window.location='{{route('user.show', ['slug_token'=>$data->user_slug_token])}}'">View User Account</button>
                            @endif
                        </div>
                        <hr>

                      </div>
                      <!-- /- Column left end -->

                      <!-- Column right start -->
                      <div style="padding-left:40px;" class="col-xs-12 col-sm-7 col-md-8 col-lg-9">

                          <br>
                          <br>
                          <br>

                        <form class="forms-sample" method="POST" enctype="multipart/form-data" action="{{route('employee.update', ['slug_token'=>$data->employee_slug_token])}}">
                        @method('PUT')
                        @csrf
                        <div class="row">
                          <div class="col-md-6 col-lg-6">
                              <div class="form-group row">
                                <label for="employee_no" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Employee No.</label>
                                <div class="col-sm-9">
                                  <input type="text" class="form-control @error('employee_no') is-invalid @enderror" name="employee_no" id="employee_no" placeholder="Employee No."  value="{{ (old('employee_no')) ? old('employee_no') : $data->employee_no }}">
                              
                                  @error('employee_no')
                                    <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>
                            
                              <div class="form-group row">
                                <label for="first_name" class="col-sm-3 col-form-label"><span class="text-danger">*</span>First Name</label>
                                <div class="col-sm-9">
                                  <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" id="first_name" placeholder="First Name"  value="{{ (old('first_name')) ? old('first_name') : $data->employee_first_name }}">
      
                                    @error('first_name')
                                      <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                      </span>
                                    @enderror
                                </div>
                              </div>
                              <div class="form-group row">
                                <label for="last_name" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Last Name</label>
                                <div class="col-sm-9">
                                  <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" id="last_name" placeholder="Last Name"  value="{{ (old('last_name')) ? old('last_name') : $data->employee_last_name }}">
                              
                                  @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                                </div>
                              </div>
                              <div class="form-group row">
                                  <label for="department" class="col-sm-3 col-form-label"><span class="text-danger">*</span> Department</label>
                                  <div class="col-sm-9">
                                      <select class="form-control @error('department') is-invalid @enderror" name="department" id="department" >
                                          <option value="">-Department-</option>
                                          @foreach($departments as $department)
                                              <option @if($department->id == old('department') || $department->id == $data->employee_department_id) selected @endif value="{{$department->id}}">{{ucfirst($department->name)}}</option>
                                          @endforeach
                                          @if(old('department'))
                                              @if($department->id != old('department'))
                                                  <option selected value="{{old('department')}}">{{ucfirst(old('department'))}}</option>
                                              @endif
                                          @endif
                                      </select>
                                      @error('department')
                                      <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                    </span>
                                      @enderror
                                  </div>
                              </div>

                          </div>
                          <div class="col-md-6 col-lg-6">
                            <div class="form-group row">
                              <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                              <div class="col-sm-9">
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" placeholder="Phone"  value="{{ (old('phone')) ? old('phone'): $data->employee_phone }}">
                            
                                @error('phone')
                                  <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                              </div>
                            </div>
                            <div class="form-group row">
                              <label for="address" class="col-sm-3 col-form-label">Address</label>
                              <div class="col-sm-9">
                                  <textarea style="height:135px" class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Address"  >{{ (old('address')) ? old('address'): $data->employee_address }}</textarea>

                                @error('address')
                                  <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                  </span>
                                @enderror
                              </div>
                            </div>



                            <div style="margin-top:33.5px" class="form-group row">
                              <label for="is_active" class="col-sm-3 col-form-label"><span class="text-danger">*</span> Is Active</label>
                              <div class="col-sm-9">
                                  <select class="form-control @error('is_active') is-invalid @enderror" name="is_active" id="is_active" >
                                    <option @if($data->employee_is_active == old('is_active') || $data->employee_is_active == 1) selected @endif value="1">YES</option>
                                    <option @if($data->employee_is_active == old('is_active') || $data->employee_is_active == 0) selected @endif value="0">NO</option>
                                  </select>                                  
                                  @error('is_active')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                  @enderror
                              </div>
                            </div>
                          </div>
                        </div>
                        
                          <div class="text-right">
                            <div class="form-group">
                              <div class="">
                                <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Save</button>
                              </div>
                            </div>
                          </div>
                        
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


<!-- MODAL -->
<div id="form-upload_photo" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Upload New Photo</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" method="POST" action="{{route('employee.upload_new_photo', ['slug_token'=>$data->employee_slug_token])}}" enctype="multipart/form-data">
         @csrf
          <div class="row">
            <div class="col-md-9 offset-md-1">
              <div class="form-group row">
                <label for="upload_photo" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Upload Photo <small>(100x100 max of 2MB)</small></label>
                <div class="col-sm-9">
                    <input type="file" class="form-control @error('upload_photo') is-invalid @enderror" accept="image/*" name="upload_photo" id="upload_photo" placeholder="Upload Photo">
                    @error('upload_photo')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                          </span>
                    @enderror
                </div>
              </div>

              <div class="text-right">
                <div class="form-group">
                    <button class="btn btn-danger" data-dismiss="modal"><i class="mdi mdi-close"></i> Cancel</button>
                    <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Upload</button>
                </div>
              </div>
            </div>
          </div>
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
    @error('upload_photo')
      $('#form-upload_photo').modal('show');
    @enderror
    
    $.ajaxSetup({
        headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    });

    load_select2();
    function load_select2(){
        $('#department').select2(select2_custom_option_dynamic());
        $('#is_active').select2(select2_custom_option_static());
        load_select2_custom_layout();
    }

  });
</script>
@endsection