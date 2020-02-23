@extends('layouts.master')
@section('title', 'Scanned Barcode List')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 stretch-card">
            <div class="card">
               <div class="card-header">Scanned Barcode List</div>
               <div class="card-body datatables-custom-loader">
                    <div id="datatables-container" class="">
                        <table id="id-data_table" class="table dataTable no-footer" role="grid">
                            <thead>
                                <tr role="row">
                                    <th>Barcode</th>
                                    <th>Status</th>
                                    <th>Uploaded By</th>
                                    <th>Date Uploaded</th>
                                </tr>
                            </thead>
                        </table>
                    </div>    
               </div>
            </div>
         </div>
      </div>
   </div>


@endsection

@section('customjs')
    <script type="text/javascript" src="{{asset('plugins/datatables/custom-option.js')}}"></script>

  <script type="text/javascript">
    $(document).ready(function (){

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    
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
                    url : "{{ route('uploaded_data.get_scanned_barcode_list') }}",
                    type: 'GET',
                    data: function (f) {

                    }
                },	  
                columns: [
                    { data: 'barcode', sortable:true },
                    { data: 'status', sortable:true },
                    { data: 'added_by', sortable:true },
                    { data: 'date_scan', sortable:false },
                ],
                order: [[ 3, "desc" ]],
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