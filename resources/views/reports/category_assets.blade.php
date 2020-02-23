@extends('layouts.master')
@section('title', 'Category Assets Report - PNOC')
@section('customcss')
<link rel="stylesheet" type="text/css" href="{{asset('plugins/jquery-datepicker/jquery-daterangepicker.css')}}" />
@endsection

@section('content')
   <div class="content-wrapper">
   
      <div class="row">
         <div class="col-lg-12 stretch-card">
            <div class="card">
               <div class="card-header">Category Assets Report</div>
               <div class="card-body datatables-custom-loader">
                    <div id="search-form_container">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="employee">Category</label>
                                        <div class="input-group">
                                            <select style="border: 1px solid #aeaeae;" class="form-control" name="category" id="category">
                                            <option value="">-Category-</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="location">Location</label>
                                        <div class="input-group">
                                            <select style="border: 1px solid #aeaeae;" class="form-control" name="location" id="location">
                                            <option value="">-Location-</option>
                                            @foreach($locations as $location)
                                                <option value="{{$location->id}}">{{ucfirst($location->branch_name)}}</option>
                                            @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                    <label for="property_no">Property No.</label>
                                    <input type="text" class="form-control" name="property_no" id="property_no" placeholder="Property No.">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                    <label for="date_range_pick">Date Range</label>
                                    <input type="text" class="form-control" name="date_range_pick" id="date_range_pick" placeholder="From - To" readonly="true">
                                    </div>
                                </div>
                            </div>
                        <div class="col-lg-12 text-right">
                            <div class="form-group">
                                
                                <!--  <a style="position: relative; z-index: 999;" href="javascript:void(0);" class="btn btn-danger btn-icon-text btn-pdf-download">
                           Export to PDF <i class="mdi mdi mdi-file-pdf btn-icon-append"></i>
                         </a> -->
                                <a style="position: relative; z-index: 999;" href="javascript:void(0);" class="btn btn-primary btn-icon-text btn-print-report">
                                    <i class="mdi mdi mdi-printer btn-icon-append"></i>
                                    Print Report
                                </a>
                                <a style="position: relative; z-index: 999;" href="javascript:void(0);" class="btn btn-primary btn-icon-text btn-excel-download">
                                    <i class="mdi mdi mdi-file-export btn-icon-append"></i>
                                    Export to Excel
                                </a>
                                <button type="button" class="btn btn-secondary btn-icon-text" id="search-btn_submit">
                                    Search <i class="mdi mdi-magnify"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="" id="search_result_container">
                        <table class="table" id="id-data_table">
                            <thead>
                                <tr>
                                    <th>Asset No.</th>
                                    <th>Property No.</th>
                                    <th>Item Description</th>
                                    <th>Acquisition Cost</th>
                                    <th>Location</th>
                                    <th>Employee</th>
                                    <th>PO No.</th>
                                    <th>Supplier</th>
                                    <th>Date Acquired</th>
                                    <th>Date Added</th>

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

<script type="text/javascript" src="{{asset('plugins/jquery-datepicker/moment.min.js')}}"></script>
<script type="text/javascript" src="{{asset('plugins/jquery-datepicker/jquery-daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{asset('plugins/datatables/custom-option.js')}}"></script>
<script type="text/javascript" src="{{asset('plugins/select2/custom-option.js')}}"></script>

<script type="text/javascript">
   $(document).ready(function() {

      $.ajaxSetup({
         headers: {
               'X-CSRF-TOKEN': '{{ csrf_token() }}'
         }
      });

      $('#date_range_pick[readonly]').css({'background-color':'#FFFFFF'});
      $('#date_range_pick').daterangepicker({
		showDropdowns: true,
		minYear: 1980,
		maxYear: parseInt(moment().format('YYYY')),
		locale: {
			  cancelLabel: 'Clear'
		},
		"autoUpdateInput": false,
		"autoApply":false,
		// maxDate:moment(), 
    });
    $('#date_range_pick').on('apply.daterangepicker', function(ev, picker) {
		 $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });
    $('#date_range_pick').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });
    


    load_datatable();
    $('#id-data_table').DataTable().draw(true);
    $('#search-btn_submit').on('click', function (){
        $('#id-data_table').DataTable().draw(true);
    });

    function load_datatable(){
        $('#id-data_table').DataTable({
            searching: false,
            processing: false,
            serverSide: true,
            responsive: true,
            autoWidth : false, 
            order: [[9, 'desc'], [0, 'asc']],
            pageLength: 10,
            lengthMenu: [
               [10, 25, 50, 100, -1],
               ['10', '25', '50', '100', 'Show All']
            ],
            // dom   : "<'row'<'col-sm-12 float-right margin-bottom20'B>><'row'<'col-sm-12 col-xs-12 text-right'l>><'row'<'col-sm-12'tr>><'row'<'col-sm-5 col-xs-6'i><'col-sm-7 col-xs-6'p>>",
            ajax: {
                url : "{{ route('report.get_category_assets') }}",
                type: 'GET',
                data: function (f) {
                    f.category_id       = $("#category").val();
                    f.location_id       = $("#location").val();
                    f.property_number   = $("#property_no").val();
                    f.date_range_pick   = $("#date_range_pick").val();
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
                { data: 'date_added', sortable:true },

            ],
            fnPreDrawCallback: function (){
                datatables_custom_loader_show();
            },
            fnDrawCallback: function(){
                datatables_custom_loader_hide();
                datatables_total_amount_acquisition_cost();
            },
        });
        
        
        /*  3 param => container_id, small device top margin, lg device top margin 
            default param => datatable_container_fix_adjust_top(container_id='search_result_container', sm_px='0px', lg_px='-70px')*/
            datatable_container_fix_adjust_top();
    }
    

   });

   //Print button
   $(".btn-print-report").on('click', function(){
        var url         = '{{route("report.print_category_report")}}';
        var filters     = {
            'category_id'       : $("#category").val(),
            'location_id'       : $("#location").val(),
            'property_number'   : $("#property_no").val(),
            'date_range_pick'   : $("#date_range_pick").val()
        };
        print_reports(filters, url);
    });

   // Export Button
   $('.btn-excel-download').on('click',function(){
	    var query = {
	    	category_id: $("#category").val(),
	        location_id: $("#location").val(),
	    	property_number : $('#property_no').val(),
	        date_range_pick : $('#date_range_pick').val(),
	    }
	    var url = "{{URL::to('report/export_category_assets')}}?" + $.param(query)

	   window.location = url;
	});
    $('.btn-pdf-download').on('click',function(){
	    var query = {
	    	category_id: $("#category").val(),
	        location_id: $("#location").val(),
	    	property_number : $('#property_no').val(),
	        date_range_pick : $('#date_range_pick').val(),
	    }
	    var url = "{{URL::to('report/generate_pdf_category_assets')}}?" + $.param(query)

	   window.location = url;
	});
   load_select2();
   function load_select2(){
       $('#location').select2(select2_custom_option_static());
       $('#category').select2(select2_custom_option_static());
       load_select2_custom_layout();
   }
  </script>
@endsection