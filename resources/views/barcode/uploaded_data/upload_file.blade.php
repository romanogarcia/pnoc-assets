@extends('layouts.master')
@section('title', 'Uploaded Barcode File')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 stretch-card">
            <div class="card">
               <div class="card-header">Uploaded Barcode File</div>
               <div class="card-body datatables-custom-loader">

                    <p class="card-title text-right">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#form-add_data">
                        <i class="mdi mdi-plus"></i> UPLOAD
                        </button>
                    </p>
                    

                    <div id="datatables-container" class="">
                        <table id="id-data_table" class="table dataTable no-footer" role="grid">
                            <thead>
                                <tr role="row">
                                    <th>File</th>
                                    <th>Size</th>
                                    <th>Description</th>
                                    <th>Uploaded By</th>
                                    <th>Date Uploaded</th>
                                    <th class="text-center">Action</th>
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
        <h4 class="modal-title">Upload Scanned Barcode</h4>
        <button type="button" class="close" data-dismiss="modal">×</button>
      </div>
      <div class="modal-body">
        <form method="POST" enctype="multipart/form-data" accept-charset="utf-8" action="{{ route('uploaded_data.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="form-group">
                        <label for="location"><span class="text-danger">*</span> Current Location</label>
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
                    <div class="form-group">
                        <label for="file"><span class="text-danger">*</span> Upload File <small>(.txt)</small></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" accept=".txt" id="file" placeholder="Upload File">
                        @error('file')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" name="description" id="description" placeholder="Description (optional)" rows="3">{{old('description')}}</textarea>
                    </div>

                    <div class="text-right">
                        <div class="form-group">
                        <div class="">
                            <button class="btn btn-danger" data-dismiss="modal"><i class="mdi mdi-close"></i> Cancel</button>
                            <button type="submit" class="btn btn-success mr-2"><i class="mdi mdi-check"></i> Submit</button>
                        </div>
                        </div>
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
    <script type="text/javascript" src="{{asset('plugins/datatables/custom-option.js')}}"></script>
    <script type="text/javascript" src="{{asset('plugins/select2/custom-option.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function (){
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
            $('#location').select2(select2_custom_option_dynamic());
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
                pageLength: -1,
                lengthMenu: [
                    [10, 25, 50, 100, -1],
                    ['10', '25', '50', '100', 'Show All']
                ],
                // dom   : "<'row'<'col-sm-12 float-right margin-bottom20'B>><'row'<'col-sm-12 col-xs-12 text-right'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-5 col-xs-6'i><'col-sm-7 col-xs-6'p>>",
                ajax: {
                    url : "{{ route('uploaded_data.get_uploaded_file') }}",
                    type: 'GET',
                    data: function (f) {

                    }
                },	  
                columns: [
                    { data: 'file', sortable:true },
                    { data: 'file_size', sortable:true },
                    { data: 'description', sortable:true },
                    { data: 'uploaded_by', sortable:true },
                    { data: 'date_uploaded', sortable:true },
                    { data: 'action', sortable:false },
                ],
                order: [[ 4, "desc" ]],
                fnPreDrawCallback: function (){
                    datatables_custom_loader_show();
                },
                fnDrawCallback: function(){
                    datatables_custom_loader_hide();
                },
                
            });
            
            
            // $("#search_form_container").hide();
            if(window.matchMedia("(max-width: 992px)").matches){
                $("#search_result_container").css("margin-top", "0px");
            }else{
                $("#search_result_container").css("margin-top", "-50px");
            }
            $("#search-btn_submit").css('position', 'relative');
            $("#search-btn_submit").css('z-index', '9999');

            
            $("#id-data_table").on('click', '.btn-delete_data', function (){
                var slug_token = $(this).data('slug_token');
                var file = $(this).data('file');
                var url = '{{ route("uploaded_data.destroy", ":slug_token") }}';
                url = url.replace(':slug_token', slug_token);
                $("#modal-delete_data").find('#modal-delete_alert').html('Do you want to remove <span class="badge badge-primary">'+file+'</span> ? This file can\'t be restored anymore.');
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