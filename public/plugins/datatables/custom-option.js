var datatable_container_fix_adjust_top = function datatable_container_fix_adjust_top(container_id='search_result_container', sm_px='0px', lg_px='-70px'){
    if(window.matchMedia("(max-width: 992px)").matches){
        $("#"+container_id).css("margin-top", sm_px);
    }else{
        $("#"+container_id).css("margin-top", lg_px);
    }
}

var datatables_custom_loader_show = function datatables_custom_loader_show(loader = 'datatables-custom-loader'){
    $('.custom-datatables-proccessing-loader').remove();
    $("."+loader).append(`<div class="custom-datatables-proccessing-loader">
        <h2 class"dpl-loading-label"><b><i class="mdi mdi-rotate-right mdi-spin"></i> Loading...</b></h2>
    </div>`);
    $("#search-btn_submit").css('z-index', '1');
    $(".btn-excel-download").css('z-index', '1');
    $(".btn-print-barcode").css('z-index', '1');
    $(".btn-print-report").css('z-index', '1');
    
    var dt_container = $('.'+loader).find("#id-data_table").parent(".col-sm-12");
    
    dt_container.addClass('table-responsive');
    dt_container.css('overflow-x', 'auto');
    dt_container.css('max-height', '600px');
    
    var dt_thead    = dt_container.find('thead');
    var dt_th       = dt_thead.find('th');
    dt_th.css({
        'position'              : 'sticky',
        'top'                   : '-1px',
        'background'            : '#FFFFFF',
        'border-top'            : '1px solid rgba(151, 151, 151, 0.18)',
    });
}

var datatables_custom_loader_hide = function datatables_custom_loader_hide(loader = 'datatables-custom-loader'){
    $("."+loader).find('.custom-datatables-proccessing-loader').hide();
    $("#search-btn_submit").css('z-index', '9');
    $(".btn-excel-download").css('z-index', '9');
    $(".btn-print-barcode").css('z-index', '9');
    $(".btn-print-report").css('z-index', '9');
}

var datatables_total_amount_acquisition_cost = function datatables_total_amount_acquisition_cost(class_acq_cost= 'datatables-acquisition_cost', id_total_acq='datatables-total_acquisition_cost', id_data_tables='id-data_table'){
    var total_amount = 0;
    $("."+class_acq_cost).each(function (){
        var amount = $(this).data('amount');
        total_amount += amount;
    });
    total_amount = total_amount.toFixed(2);
    total_amount = add_comma(total_amount);
    $("#"+id_data_tables+'_wrapper').find('#'+id_data_tables+'_total_acq_wrapper').remove();
    $("#"+id_data_tables+'_wrapper').append(`<div class="text-right" id="`+id_data_tables+`_total_acq_wrapper" style="padding: 20px;">Total Amount: `+total_amount+`</div>`);
}


function add_comma(num) {
    var str = num.toString().split('.');
    if (str[0].length >= 4) {
        //add comma every 3 digits befor decimal
        str[0] = str[0].replace(/(\d)(?=(\d{3})+$)/g, '$1,');
    }
    /* Optional formating for decimal places
    if (str[1] && str[1].length >= 4) {
        //add space every 3 digits after decimal
        str[1] = str[1].replace(/(\d{3})/g, '$1 ');
    }*/
    return str.join('.');
}

var print_reports = function print_reports(filters=[], url){
    var window_url = url+'?'+$.param(filters);
    open_window_print_report(window_url);
}

var open_window_print_report = function open_window_print_report(url, title='Print Report'){
    var newWindow     = window.open(url, title,"width="+screen.availWidth+",height="+screen.availHeight);
    newWindow.document.close();
    newWindow.focus();
    // newWindow.print();
    // newWindow.close();
 }