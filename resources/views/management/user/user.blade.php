@extends('layouts.master')
@section('title', 'User Information - PNOC')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
    
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">User Information</div>
               <div class="card-body">
               
                  <div class="row">
                     <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
                        <h3>User Details</h3>
                        <div class="py-4">
                          <p class="clearfix">
                            <span class="float-left">
                              Account Lock
                            </span>
                            <span class="float-right text-muted">
                              @if($data->account_lock == 1)
                                <label class="badge badge-danger">YES</label>
                              @else
                                <label class="badge badge-success">NO</label>
                              @endif
                            </span>
                          </p>
                          <p class="clearfix">
                            <span class="float-left">
                              Username
                            </span>
                            <span class="float-right text-muted">
                              {{$data->username}}
                            </span>
                          </p>
                          <p class="clearfix">
                            <span class="float-left">
                              Email
                            </span>
                            <span class="float-right text-muted">
                              {{$data->email}}
                            </span>
                          </p>
                          <p class="clearfix">
                            <span class="float-left">
                              Role
                            </span>
                            <span class="float-right text-muted">
                              {{$roles}}
                            </span>
                          </p>
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

                            @if($data->employee_slug_token)
                              <button class="btn btn-primary btn-block mb-2" type="button" onclick="window.location='{{route('employee.show', ['slug_token'=>$data->employee_slug_token])}}'">View Employee Profile</button>
                            @endif
                        </div>
                     </div>
                     <div style="padding-left:40px;" class="col-xs-12 col-sm-7 col-md-8 col-lg-9">
                        <div class="row">
                           <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                              <h3>Update Details</h3>
                              <br>
                              <form class="forms-sample" method="POST" enctype="multipart/form-data" action="{{route('user.update', ['slug_token'=>$data->user_slug_token])}}">
                              @method('PUT')
                              @csrf      
                              <div class="form-group row">
                                 <label for="employee" class="col-sm-3 col-form-label">Employee</label>
                                 <div class="col-sm-9">
                                    <select  class="form-control @error('employee') is-invalid @enderror" name="employee" id="employee" >
                                    <option value="">-Employee-</option>
                                    @foreach($employees as $employee)
                                       <option @if($employee->id == old('employee') || $employee->id == $data->employee_id) selected @endif value="{{$employee->id}}">{{$employee->employee_no}} | {{ucwords($employee->first_name)}} {{ucwords($employee->last_name)}}</option>
                                    @endforeach
                                    </select>
                                    @error('employee')
                                       <span class="invalid-feedback" role="alert">
                                       <strong>{{ $message }}</strong>
                                       </span>
                                    @enderror
                                 </div>
                              </div>
                              
                                 <div class="form-group row">
                                    <label for="access_role" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Role Type</label>
                                    <div class="col-sm-9">
                                       <select  class="form-control @error('access_role') is-invalid @enderror" name="access_role" id="access_role" >
                                       <option value="">-Access Role-</option>
                                       @foreach($access_role as $role)
                                          <option @if($role->id == old('access_role') || $role->id == $data->employee_role_id) selected @endif value="{{$role->id}}">{{ucfirst($role->role_name)}}</option>
                                       @endforeach
                                       </select>
                                       @error('access_role')
                                          <span class="invalid-feedback" role="alert">
                                          <strong>{{ $message }}</strong>
                                          </span>
                                       @enderror
                                    </div>
                                 </div>

                                 <div class="form-group row">
                                       <label for="email" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Email</label>
                                       <div class="col-sm-9">
                                       <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Email" value="{{ (old('email')) ? old('email'):$data->email }}">
                                       
                                       @error('email')
                                          <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                          </span>
                                       @enderror
                                       </div>
                                 </div>

                                 <div class="form-group row">
                                       <label for="username" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Username</label>
                                       <div class="col-sm-9">
                                       <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" placeholder="Username" value="{{ (old('username')) ? old('username'):$data->username }}">
                                       
                                       @error('username')
                                          <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                          </span>
                                       @enderror
                                       </div>
                                 </div>

                                 <div class="form-group row">
                                    <label for="is_locked" class="col-sm-3 col-form-label"><span class="text-danger">*</span> Account Lock</label>
                                    <div class="col-sm-9">
                                       <select class="form-control @error('is_locked') is-invalid @enderror" name="is_locked" id="is_locked" >
                                          <option @if($data->account_lock == old('is_locked') || $data->account_lock == 1) selected @endif value="1">YES</option>
                                          <option @if($data->account_lock == old('is_locked') || $data->account_lock == 0) selected @endif value="0">NO</option>
                                       </select>                                  
                                       @error('is_locked')
                                          <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                          </span>
                                       @enderror
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
                           <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                              <h3>Set New Password</h3>
                              <br>
                              <form class="forms-sample" method="POST" enctype="multipart/form-data" action="{{route('user.set_new_password', ['slug_token'=>$data->user_slug_token])}}">
                                 @csrf  

                                 <div class="form-group row">
                                       <label for="password" class="col-sm-3 col-form-label"><span class="text-danger">*</span>New Password</label>
                                       <div class="col-sm-9">
                                       <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="New Password" value="{{old('password')}}">
                                       
                                       @error('password')
                                          <span class="invalid-feedback" role="alert">
                                             <strong>{{ $message }}</strong>
                                          </span>
                                       @enderror
                                       </div>
                                 </div>
                                 <div class="form-group row">
                                       <label for="password_confirmation" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Confirm New Password</label>
                                       <div class="col-sm-9">
                                       <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm New Password" value="{{old('password_confirmation')}}">
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
                        </div>
                     </div>
                  </div>

               </div>
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


   load_select2();
   function load_select2(){
      $('#access_role').select2(select2_custom_option_static());
      $('#is_locked').select2(select2_custom_option_static());
      $('#employee').select2(select2_custom_option_static());
      load_select2_custom_layout();
   }
});
</script>
@endsection