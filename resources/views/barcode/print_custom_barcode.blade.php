@extends('layouts.master')
@section('title', 'Print Custom Barcode - PNOC')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
      <div class="row">
         <div class="col-md-12 stretch-card">
            <div class="card">
               <div class="card-header">Print Custom Barcode</div>
               <div class="card-body">
                  <div class="row">
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                        <div id="search-form_container">
                           <div class="form-group">
                              <label for="print_layout_type"><span class="text-danger">*</span> Print Layout Type</label>
                              <select class="form-control" id="print_layout_type">
                                 <option value="default">Default 1 barcode</option>
                                 <option value="6_small_barcode">6 Small barcode</option>
                                 <option value="2_onehalf_2_small_barcode">2 one/half and 2 small barcode</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                           <div id="print-preview_container">
                           
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
   <script type="text/javascript">
      $(document).ready(function (){
         $("#print-preview_container").html(get_default_barcode());
         $("#print_layout_type").on('change', function (){
            var type       = $(this).val();
            var container  = $("#print-preview_container");
            var content    = '';

            if(type == 'default')
               content = get_default_barcode();
            else if(type == '6_small_barcode')
               content = get_6_small_barcode();
            else if(type == '2_onehalf_2_small_barcode')
               content = get_2_onehalf_2_small();

            container.html(content);

         });

         $("#print-preview_container").on('click', '.btn-print_', function (){
            var validate      = false;
            var data_array    = [];
            var layout_type   = $(this).data('layout_type');
            $(".barcode-input").each(function (){
               var value   = $(this).val();
               data_array.push(value);
            });

            var validate_data_array = JSON.stringify(data_array);
            for(var key in data_array){
               if(data_array[key] != ''){
                  validate = true;
                  break;
               }
            }

            if(!validate){
               $(".required-alert").show();
            }else{
               $(".required-alert").hide();
               var window_url = '{{route("uploaded_data.print_preview_custom_barcode", ["data_array"=>"", "layout_type"=>""])}}'+'/'+data_array+'/'+layout_type;
               openWindowPrint(window_url);
            }

         });
         
         function openWindowPrint(url, title='Print Barcode'){
            var newWindow     = window.open(url, title,"width="+screen.availWidth+",height="+screen.availHeight);
            newWindow.document.close();
            newWindow.focus();
            newWindow.print();
            // newWindow.close();
         }

         function get_default_barcode(){
            return `
               <div class="print-preview_container_global_enc text-center">
                  <div class="paper-page print-preview_container_global_enc_input" style="border: 1px solid #bfbbbb; width: 83%; border-radius: 6px;">
                     <div class="row">
                        <div class="col-6 offset-3">
                           <input type="text" placeholder="Enter Property Number" name="barcode_input_a" class="form-control barcode-input barcode-input-default">
                        </div>
                     </div>
                  </div>
                  <div class="no-print">
                     <div class="required-alert" style="display: none;"><label class="text-danger">Please fill atleast 1 field</label></div>
                     <button type="button" title="Click to Print" class="btn-print_ btn btn-primary mr-2" data-layout_type="default"><i class="mdi mdi-printer"></i> Print</button>
                  </div>
               </div>
            `;
         }

         function get_6_small_barcode(){
            return `<div class="print-preview_container_global_enc text-center">
                              <div class="paper-page print-preview_container_global_enc_input" style="border: 1px solid #bfbbbb; width: 83%; border-radius: 6px;">
                                 <div class="row">
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_a" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_b" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_c" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_d" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_e" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_f" class="form-control barcode-input">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="no-print">
                                 <div class="required-alert" style="display: none;"><label class="text-danger">Please fill atleast 1 field</label></div>
                                 <button type="button" title="Click to Print" class="btn-print_ btn btn-primary mr-2" data-layout_type="6_small_barcode"><i class="mdi mdi-printer"></i> Print</button>
                              </div>
                           </div>`;
         }

         function get_2_onehalf_2_small(){
            return `<div class="print-preview_container_global_enc text-center">
                              <div class="paper-page print-preview_container_global_enc_input" style="border: 1px solid #bfbbbb; width: 83%; border-radius: 6px;">
                                 <div class="row">
                                    <div class="col-8">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_a" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_b" class="form-control barcode-input">
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="col-8">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_c" class="form-control barcode-input">
                                       </div>
                                    </div>
                                    <div class="col-4">
                                       <div class="form-group">
                                          <input type="text" placeholder="Enter Property Number" name="barcode_input_d" class="form-control barcode-input">
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="no-print">
                                 <div class="required-alert" style="display: none;"><label class="text-danger">Please fill atleast 1 field</label></div>
                                 <button type="button" title="Click to Print" class="btn-print_ btn btn-primary mr-2" data-layout_type="2_onehalf_2_small"><i class="mdi mdi-printer"></i> Print</button>
                              </div>
                           </div>`;
         }
      });
   </script>
@endsection