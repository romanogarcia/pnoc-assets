@extends('layouts.master')
@section('title', 'Users - PNOC')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">Users</div>
               <div class="card-body datatables-custom-loader">
                  <p class="card-title text-right">
                     <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add_users">
                     <i class="mdi mdi-plus"></i> ADD
                     </button>
                  </p>

                  <div id="datatables-container" class="">
                     <table id="id-data_table" class="table dataTable no-footer" role="grid">
                        <thead>
                           <tr role="row">
                              <th>Username</th>
                              <th>Email</th>
                              <th>Roles</th>
                              <th>Account Lock</th>
                              <th>Date Added</th>
                              <th class="text-center" style="width: 193px;">Action</th>
                           </tr>
                        </thead>
                     </table>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>

<!--MODALS-->
<!--Add User modal-->
   <div id="add_users" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title">Add User</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <form class="forms-sample" enctype="multipart/form-data" method="POST" action="{{ route('user.store') }}">
                  @csrf
                     
                     
                     <div class="form-group row">
                        <label for="employee" class="col-sm-3 col-form-label">Employee</label>
                        <div class="col-sm-9">
                           <select  class="form-control @error('employee') is-invalid @enderror" name="employee" id="employee" >
                           <option value="">-Employee-</option>
                           @foreach($employees as $employee)
                              <option @if($employee->id == old('employee')) selected @endif value="{{$employee->id}}">{{$employee->employee_no}} | {{ucwords($employee->first_name)}} {{ucwords($employee->last_name)}}</option>
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
                           @foreach($roles as $role)
                              <option @if($role->id == old('access_role')) selected @endif value="{{$role->id}}">{{ucfirst($role->role_name)}}</option>
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
                          <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" placeholder="Email" value="{{old('email')}}">
                        
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
                          <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" id="username" placeholder="Username" value="{{old('username')}}">
                        
                          @error('username')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Password</label>
                        <div class="col-sm-9">
                          <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Password" value="{{old('password')}}">
                        
                          @error('password')
                            <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                            </span>
                          @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Confirm Password</label>
                        <div class="col-sm-9">
                          <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" value="{{old('password_confirmation')}}">
                        </div>
                    </div>
                     <div class="text-right">
                        <div class="form-group">
                        <div class="">
                           <button class="btn btn-danger" type="button" data-dismiss="modal"><i class="mdi mdi-close"></i> Cancel</button>
                           <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Submit</button>
                        </div>
                        </div>
                     </div>
              </form>
            </div>
          </div>
        </div>
      </div>

<!-- Activate/Deactivate Modal -->

<div class="modal fade" id="modal-delete_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content">
     <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Update record</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
     </div>
     <div class="modal-body" id="modal-delete_alert">
        Are you sure do you want to delete this row?
     </div>
     <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <form action="" id="form-delete_data" method="GET">
           @csrf
           <button type="button" class="btn btn-success" id="form-btn_delete_submit">Yes</button>
        </form>
     </div>
  </div>
  </div>
</div>
@endsection

      
@section('customjs')
<script type="text/javascript" src="{{asset('plugins/select2/custom-option.js')}}"></script>
<script type="text/javascript" src="{{asset('plugins/datatables/custom-option.js')}}"></script>

<script type="text/javascript">
   $(document).ready(function() {

      @if(!$errors->isEmpty())
         $("#add_users").modal('show');
      @endif

      $.ajaxSetup({
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         }
      });

      load_select2();
      function load_select2(){
         $('#access_role').select2(select2_custom_option_static());
         $('#employee').select2(select2_custom_option_static());
         load_select2_custom_layout();
      }

      load_datatable();
      function load_datatable(){
         $('#id-data_table').DataTable({
            searching: true,
            processing: false,
            serverSide: true,
            responsive: true,
            autoWidth : false,
            order: [[4, 'desc'],[3, 'desc'], [0, 'asc']],
            pageLength: 10,
            lengthMenu: [
               [10, 25, 50, 100, -1],
               ['10', '25', '50', '100', 'Show All']
            ],
            // dom   : "<'row'<'col-sm-12 float-right margin-bottom20'B>><'row'<'col-sm-12 col-xs-12 text-right'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-5 col-xs-6'i><'col-sm-7 col-xs-6'p>>",
            ajax: {
               url : "{{ route('user.get_data') }}",
               type: 'GET',
               data: function (request) {
                  // Data to pass in serverside
               }
            },	  
            columns: [
               { data: 'username', sortable:true },
               { data: 'email', sortable:true },
               { data: 'roles', sortable:true },
               { data: 'account_lock', sortable:true },
               { data: 'date_added', sortable:true },
               { data: 'action', sortable:false },
            ],
            fnPreDrawCallback: function (){
               datatables_custom_loader_show();
            },
            fnDrawCallback: function(){
               datatables_custom_loader_hide();
            },
         });

         $("#id-data_table").on('click', '.btn-delete_data', function (){
            if($(this).data('status') == 0){
               var status = "Deactivate";
            }else{
               var status = "Activate";
            }
            var slug_token = $(this).data('slug_token');
            var employee_id = $(this).data('employee_id');
            var url = '{{ route("user.update_status", ":slug_token") }}';
            url = url.replace(':slug_token', slug_token);
            $("#modal-delete_data").find('#modal-delete_alert').html('Do you want to '+status+' this user <span class="badge badge-primary">'+employee_id+'</span>?');
            $("#form-delete_data").attr('action', url);
            $("#modal-delete_data").modal('show');
         });

         $("#form-btn_delete_submit").on('click', function (){
            $("#form-delete_data").submit();
         });
      }


   });
</script>
@endsection