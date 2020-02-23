var load_select2_custom_layout = function load_select2_custom_layout(){
    var s = $(".select2-selection.select2-selection--single");
    var s_arrow = s.find(".select2-selection__arrow");
    var selected_box = $(".select2-selection__rendered");
    var select_option = $(".select2-results__options").find('.select2-results__option');

    select_option.css('padding-left', '22px');
    selected_box.css('padding-left', '22px');
    s_arrow.css('top', '8px');
    s.css('height', '2.875rem');
    s.css('padding-top', '8px');


    
    
    $(document).on('click', '.select-search_-click_add_data', function (){
        var c = $(this).parents('.select2-dropdown.select2-dropdown--below');
        if(!c.html()){
            var c = $(this).parents('.select2-dropdown.select2-dropdown--above');
        }

        var search_field = c.find('.select2-search__field').val();
    
        var get_id = c.find(".select2-results__options");
        var id_encode = get_id.attr('id'); //select2-category-results
        var id = '';
    
        for(var x=8; x < id_encode.length-1; x++){
            if(id_encode[x] != '-')
                id = id+''+id_encode[x];
            else
                break;
        }
    
        var parent_id = $("#"+id);
    
        var option_container = '.select2-selection__rendered';
        parent_id.find(option_container).html(search_field);
        parent_id.find(option_container).attr('title',search_field);
        parent_id.append('<option value="'+search_field+'">'+search_field+'</option>');
        parent_id.val($('#'+id+' option:last-child').val());
        parent_id.change();
    });
}

var select2_custom_option_dynamic = function select2_custom_option_dynamic(){
    return {
        selectONclose: false,
        height: 'resolve', //override the height
        width: '100%', //override the width
        language: {
        noResults: function(){
              return '<a href="javascript:void(0);" class="select-search_-click_add_data"><i class="mdi mdi-plus"></i> Click here to add</a>';
           }
        },
        escapeMarkup: function (markup) {
           return markup;
        }
    };
}

var select2_custom_option_static = function select2_custom_option_static(){
    return {
        selectONclose: false,
        height: 'resolve', //override the height
        width: '100%', //override the width
        escapeMarkup: function (markup) {
           return markup;
        }
     };
}