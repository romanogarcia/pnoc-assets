@extends('layouts.master')
@section('title', 'Accountable Employees')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 stretch-card">
            <div class="card">
               <div class="card-header">Accountable Employees</div>
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
                              <th>Photo</th>
                              <th>Name</th>
                              <th>Employee No.</th>
                              <th>Phone</th>
                              <th>Address</th>
                              <th>Department</th>
                              <th>Is Active</th>
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
         <h4 class="modal-title">Add Accountable Employee</h4>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
      <form class="forms-sample" enctype="multipart/form-data" method="POST" action="{{ route('employee.store') }}">
            @csrf
               <div class="form-group row">
                  <label for="employee_no" class="col-sm-3 col-form-label"><span class="text-danger">*</span>Employee No.</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control @error('employee_no') is-invalid @enderror" name="employee_no" id="employee_no" placeholder="Employee No." value="{{old('employee_no')}}">
                  
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
                     <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" id="first_name" placeholder="First Name" value="{{old('first_name')}}">

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
                     <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" id="last_name" placeholder="Last Name" value="{{old('last_name')}}">
                  
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
                           <option @if($department->id == old('department')) selected @endif value="{{$department->id}}">{{ucfirst($department->name)}}</option>
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
               <div class="form-group row">
                  <label for="address" class="col-sm-3 col-form-label">Address</label>
                  <div class="col-sm-9">
                     <textarea style="height:100px"  class="form-control @error('address') is-invalid @enderror" name="address" id="address" placeholder="Address" value="{{old('address')}}"></textarea>

                     @error('address')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                     @enderror
                  </div>
               </div>
               <div class="form-group row">
                  <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                  <div class="col-sm-9">
                     <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" id="phone" placeholder="Phone" value="{{old('phone')}}">

                     @error('phone')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                     @enderror
                  </div>
               </div>

               <div class="form-group row">
                  <label for="upload_photo" class="col-sm-3 col-form-label"><span class="text-danger"></span>Upload Photo</label>
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
               <div class="">
                  <button class="btn btn-danger" data-dismiss="modal" type="button"><i class="mdi mdi-close"></i> Cancel</button>
                  <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Submit</button>
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
         $('#department').select2(select2_custom_option_dynamic());
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
            order: [[7, 'desc'], [6, 'desc'],[1, 'asc']],
            pageLength: 10,
            lengthMenu: [
               [10, 25, 50, 100, -1],
               ['10', '25', '50', '100', 'Show All']
            ],
            // dom   : "<'row'<'col-sm-12 float-right margin-bottom20'B>><'row'<'col-sm-12 col-xs-12 text-right'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-5 col-xs-6'i><'col-sm-7 col-xs-6'p>>",
            ajax: {
               url : "{{ route('employee.get_data') }}",
               type: 'GET',
               data: function (request) {
                  // Data to pass in serverside
               }
            },	  
            columns: [
               { data: 'photo', sortable:false },
               { data: 'name', sortable:true },
               { data: 'employee_no', sortable:true },
               { data: 'phone', sortable:true },
               { data: 'address', sortable:true },
               { data: 'department', sortable:true },
               { data: 'is_active', sortable:true },
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

      }
      
   });      
</script>
@endsection