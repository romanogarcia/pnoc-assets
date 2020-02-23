@extends('layouts.master')
@section('title', 'Dashboard - PNOC')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
   
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">Recent Assets</div>
               <div class="card-body datatables-custom-loader">
                  @if(Utility::get_current_role() == '1')
                  <p class="card-title text-right">
                     <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#form-add_data">
                     <i class="mdi mdi-plus"></i> ADD
                     </button>
                  </p>
                  @endif

                  <div id="datatables-container">
                     <table id="id-data_table" class="table dataTable no-footer" role="grid">
                        <thead>
                           <tr role="row">
                              <th>Asset No.</th>
                              <th>Property No.</th>
                              <th>Item Description</th>
                              <th>Acquisition Cost</th>
                              <th>Location</th>
                              <th>Employee</th>
                              <th>PO No.</th>
                              <th>Supplier</th>
                              <th>Date Acquired</th>
                              <th class="text-center" style="width: 160px;">Action</th>
                           </tr>
                        </thead>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

 

<!-- Add Form Modal -->
<div id="form-add_data" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add Asset</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <form class="forms-sample" method="POST" action="{{route('asset.store')}}">
         @csrf
          <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label for="category"><span class="text-danger">*</span> Category</label>
                     <div class="input-group">
                        <select class="form-control @error('category') is-invalid @enderror" name="category" id="category" >
                           <option value="">-Category-</option>
                           @foreach($categories as $category)
                              <option @if($category->id == old('category')) selected @endif value="{{$category->id}}">{{ucfirst($category->name)}}</option>
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
               
               <div class="col-md-6"></div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="property_number"><span class="text-danger">*</span>  Property Number</label>
                  <input type="text" class="form-control @error('property_number') is-invalid @enderror" name="property_number" id="property_number" placeholder="Property Number"  value="{{old('property_number')}}">
                  @error('property_number')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>

               <div class="col-md-6">
                <div class="form-group">
                  <label for="asset_no"> Asset Number</label>
                  <input type="text" class="form-control @error('asset_no') is-invalid @enderror" name="asset_no" id="asset_no" placeholder="Asset Number"  value="{{old('asset_no')}}">
                  @error('asset_no')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>
                             
               <div class="col-md-6">
                  <div class="form-group">
                     <label for="department"><span class="text-danger">*</span> Department/Office</label>
                     <div class="input-group">
                        <select class="form-control @error('department') is-invalid @enderror" name="department" id="department" >
                           <option value="">-Department-</option>
                           @foreach($departments as $department)
                              <option @if($department->id == old('department')) selected @endif value="{{$department->id}}">{{ucfirst($department->name)}}</option>
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
               
               <div class="col-md-6">
                  <div class="form-group">
                     <label for="employee"><span class="text-danger">*</span> Accountable Employee</label>
                     <div class="input-group">
                        <select style="border: 1px solid #aeaeae;" class="form-control @error('employee') is-invalid @enderror" name="employee" id="employee" >
                           <option value="">-Employee-</option>
                           @foreach($employees as $employee)
                              <option @if($employee->id == old('employee')) selected @endif value="{{$employee->id}}">{{ucfirst($employee->first_name)}} {{ucfirst($employee->last_name)}}</option>
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

               <div class="col-md-6">
                  <div class="form-group">
                     <label for="supplier"><span class="text-danger">*</span> Supplier</label>
                     <div class="input-group">
                        <select class="form-control @error('supplier') is-invalid @enderror" name="supplier" id="supplier" >
                           <option value="">-Supplier-</option>
                           @foreach($suppliers as $supplier)
                              <option @if($supplier->id == old('supplier')) selected @endif value="{{$supplier->id}}">{{ucfirst($supplier->name)}}</option>
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

               <div class="col-md-6">
                   <div class="form-group">
                      <label for="item_description"><span class="text-danger">*</span>  Item Description</label>
<!--                       <input type="text" class="form-control @error('item_description') is-invalid @enderror" name="item_description" id="item_description" placeholder="Item Description"  value="{{old('item_description')}}"> -->
                      <textarea name="item_description" class="form-control @error('item_description') is-invalid @enderror" id="item_description" rows="4" style="background: rgb(255, 255, 255) none repeat scroll 0% 0%; z-index: auto; position: relative; line-height: 14px; font-size: 14px; transition: none 0s ease 0s;"></textarea>
                      @error('item_description')
                         <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                         </span>
                      @enderror
                    </div>
              	</div>

               <div class="col-md-6">
                  <div class="form-group">
                     <label for="condition">Condition</label>
                     <input type="text" class="form-control @error('condition') is-invalid @enderror" name="condition" id="condition" placeholder="Condition"  value="{{old('condition')}}">
                     @error('condition')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                     @enderror
                  </div>
               </div>

               <!-- <div class="col-md-6">
                  <div class="form-group">
                     <label for="accounting_tag">Accounting Tag</label>
                     <input type="text" class="form-control @error('accounting_tag') is-invalid @enderror" name="accounting_tag" id="accounting_tag" placeholder="Accounting Tag"  value="{{old('accounting_tag')}}">
                     @error('accounting_tag')
                        <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                        </span>
                     @enderror
                  </div>
               </div> -->

               <div class="col-md-6">
                <div class="form-group">
                  <label for="po_no"><span class="text-danger">*</span> PO Number</label>
                  <input type="text" class="form-control @error('po_no') is-invalid @enderror" name="po_no" id="po_no" placeholder="PO Number"  value="{{old('po_no')}}">
                  @error('po_no')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label for="acquisition_cost"><span class="text-danger">*</span> Acquition Cost</label>
                  <input type="text" class="form-control @error('acquisition_cost') is-invalid @enderror" name="acquisition_cost" id="acquisition_cost" placeholder="Acquisition Cost"  value="{{old('acquisition_cost')}}">
                  @error('acquisition_cost')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>
              

               <div class="col-md-6">
                <div class="form-group">
                  <label for="report_of_waste">Report of Waste</label>
                  <input type="text" class="form-control @error('report_of_waste') is-invalid @enderror" name="report_of_waste" id="report_of_waste" placeholder="Report of Waste" value="{{old('report_of_waste')}}">
                  @error('report_of_waste')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="date_acquired"><span class="text-danger">*</span>  Date Acquired</label>
                  <input type="date" class="form-control @error('date_acquired') is-invalid @enderror" name="date_acquired" id="date_acquired" placeholder="Date Acquired"  value="{{old('date_acquired')}}">
                  @error('date_acquired')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>

               <div class="col-md-6">
                <div class="form-group">
                  <label for="disposal_number">Disposal Number</label>
                  <input type="text" class="form-control @error('disposal_number') is-invalid @enderror" name="disposal_number" id="disposal_number" placeholder="Disposal Number" value="{{old('disposal_number')}}">
                  @error('disposal_number')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label for="mr_number"><span class="text-danger">*</span> MR Number</label>
                  <input type="text" class="form-control @error('mr_number') is-invalid @enderror" name="mr_number" id="mr_number" placeholder="MR Number"  value="{{old('mr_number')}}">
                  @error('mr_number')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>

               <div class="col-md-6">
                  <div class="form-group">
                     <label for="location"><span class="text-danger">*</span> Location</label>
                     <div class="input-group">
                        <select class="form-control @error('location') is-invalid @enderror" name="location" id="location" >
                           <option value="">-Location-</option>
                           @foreach($locations as $location)
                              <option @if($location->id == old('location')) selected @endif value="{{$location->id}}">{{ucfirst($location->branch_name)}}</option>
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
               
              <div class="col-md-6">
                <div class="form-group">
                  <label for="serial_number"><span class="text-danger">*</span>  Serial Number</label>
                  <input type="text" class="form-control @error('serial_number') is-invalid @enderror" name="serial_number" id="serial_number" placeholder="Serial Number"  value="{{old('serial_number')}}">
                  @error('serial_number')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>


              <div class="col-md-6">
                <div class="form-group">
                  <label for="warranty">Warranty</label>
                  <input type="text" class="form-control @error('warranty') is-invalid @enderror" name="warranty" id="warranty" placeholder="Warranty"  value="{{old('warranty')}}">
                  @error('warranty')
                     <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                     </span>
                  @enderror
                </div>
              </div>

            
          </div>

          <div class="text-right">
            <div class="form-group">
              <div class="">
                <button class="btn btn-danger" data-dismiss="modal"><i class="mdi mdi-close"></i> Cancel</button>
                <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Submit</button>
              </div>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="modal-delete_data" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered" role="document">
   <div class="modal-content">
      <div class="modal-header">
         <h5 class="modal-title" id="exampleModalCenterTitle">Delete record</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
         <span aria-hidden="true">&times;</span>
         </button>
      </div>
      <div class="modal-body" id="modal-delete_alert">
         Are you sure do you want to delete this row?
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
         <form action="" id="form-delete_data" method="POST">
            @method('DELETE')
            @csrf
            <button type="button" class="btn btn-danger" id="form-btn_delete_submit">Delete</button>
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
         $("#form-add_data").modal('show');
      @endif

      $.ajaxSetup({
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         }
      });

      load_select2();
      function load_select2(){
         $('#category').select2(select2_custom_option_dynamic());
         $('#location').select2(select2_custom_option_dynamic());
         $('#department').select2(select2_custom_option_dynamic());
         $('#supplier').select2(select2_custom_option_dynamic());
         $('#employee').select2(select2_custom_option_static());
         load_select2_custom_layout();
      }

      

      load_datatable();
      function load_datatable(){
         var dt_table = $('#id-data_table').DataTable({
            searching: true,
            processing: false,
            serverSide: true,
            responsive: true,
            autoWidth : false,
            order: [[8, 'desc'],[0, 'desc']],
            pageLength: 10,
            lengthMenu: [
               [10, 25, 50, 100, -1],
               ['10', '25', '50', '100', 'Show All']
            ],
            // dom   : "<'row'<'col-sm-12 float-right margin-bottom20'B>><'row'<'col-sm-12 col-xs-12 text-right'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-5 col-xs-6'i><'col-sm-7 col-xs-6'p>>",
            ajax: {
               url : "{{ route('asset.get_data') }}",
               type: 'GET',
               data: function (request) {
                  // Data to pass in serverside
               }
            },	  
            columns: [
               { data: 'asset_no', sortable:true },
               { data: 'property_no', sortable:true },
               { data: 'item_description', sortable:true },
               { data: 'acquisition_cost', sortable:true },
               { data: 'location', sortable:true },
               { data: 'employee', sortable:true },
               { data: 'po_no', sortable:true },
               { data: 'supplier', sortable:true },
               { data: 'date_acquired', sortable:true },
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
            var slug_token = $(this).data('slug_token');
            var asset_id = $(this).data('asset_id');
            var url = '{{ route("asset.destroy", ":slug_token") }}';
            url = url.replace(':slug_token', slug_token);
            $("#modal-delete_data").find('#modal-delete_alert').html('Do you want to remove <span class="badge badge-primary">'+asset_id+'</span> ? This asset can\'t be restored anymore.');
            $("#form-delete_data").attr('action', url);
            $("#modal-delete_data").modal('show');
         });
        

         $("#form-btn_delete_submit").on('click', function (){
            $("#form-delete_data").submit();
         }); 
         
         $("#id-data_table").on('click', '.btn-pdf-download', function (){
            var query = {
               asset_id: $(this).data('asset_id'),
            }
	         var url = "{{URL::to('asset/generate_pdf_asset')}}?" + $.param(query)

            window.location = url;
         });
      }


   });
  </script>
@endsection