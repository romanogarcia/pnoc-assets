<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});
Auth::routes();

Route::middleware('auth')->group(function () {  

    Route::get('/dashboard', 'HomeController@index')->name('home');
    Route::get('/Documentation-PNOC-Inventory-System', 'HomeController@Documentation')->name('home.Documentation');
    
    Route::group(['prefix'=>'user'], function(){
        Route::get('/get_data',
            [
                'uses'  => 'UserController@get_data',
                'as'    => 'user.get_data'
            ]
        );
        Route::get('/update_status/{slug_token}',
            [
                'uses'  => 'UserController@update_status',
                'as'    => 'user.update_status'
            ]
        );
        Route::post('/set_new_password/{slug_token}',
            [
                'uses'  => 'UserController@set_new_password',
                'as'    => 'user.set_new_password'
            ]
        );
    });
    
    Route::get('template', function(){
    	return view('template');
    });
        
//         Route::get('documentation', function(){
//             return view('documentation');
//         });
            
    Route::group(['prefix'=>'asset'], function(){
        Route::get('/get_data',
            [
                'uses'  => 'AssetController@get_data',
                'as'    => 'asset.get_data'
            ]
        );
        Route::get('/get_print_barcode/{slug_token}',
            [
                'uses'  => 'AssetController@get_print_barcode',
                'as'    => 'asset.get_print_barcode'
            ]
        );
        Route::get('/generate_pdf_asset',
            [
                'uses'  => 'AssetController@generate_pdf_asset',
                'as'    => 'asset.generate_pdf_asset'
            ]
        );
        Route::put('/remove_history/{slug_token}',
            [
                'uses'  => 'AssetController@remove_history',
                'as'    => 'asset.remove_history'
            ]
        );
    }); 


    Route::resource('asset', 'AssetController');


    Route::group(['prefix'=>'report'], function(){
        Route::get('/asset/index',
            [
                'uses'  => 'ReportController@assets',
                'as'    => 'report.assets'
            ]
        );
        Route::get('/generate_pdf_assets',
            [
                'uses'  => 'ReportController@generate_pdf_assets',
                'as'    => 'report.generate_pdf_assets'
            ]
        );
        Route::get('/export_assets',[
            'uses'  => 'ReportController@export_assets',
            'as'    => 'report.export_assets'
        ]);
        Route::get('/report/result',
            [
                'uses'  => 'ReportController@get_report_asset',
                'as'    => 'report.get_report_asset'
            ]
        );   
        Route::get('/memorandum-receipt',
            [
                'uses'  => 'ReportController@memorandum_receipt',
                'as'    => 'report.memorandum_receipt'
            ]
        );
        Route::get('/generate_pdf_memorandum_receipt',
            [
                'uses'  => 'ReportController@generate_pdf_memorandum_receipt',
                'as'    => 'report.generate_pdf_memorandum_receipt'
            ]
        );  
        Route::get('/export_memorandum_receipt',
            [
                'uses'  => 'ReportController@export_memorandum_receipt',
                'as'    => 'report.export_memorandum_receipt'
            ]
        );  
        Route::get('/get_memorandum_receipt',
            [
                'uses'  => 'ReportController@get_memorandum_receipt',
                'as'    => 'report.get_memorandum_receipt'
            ]
        );   
        Route::get('/employee-ledger',
            [
                'uses'  => 'ReportController@employee_ledger',
                'as'    => 'report.employee_ledger'
            ]
        );   
        Route::get('/generate_pdf_employee_ledger',
            [
                'uses'  => 'ReportController@generate_pdf_employee_ledger',
                'as'    => 'report.generate_pdf_employee_ledger'
            ]
        ); 
        Route::get('/export_employee_ledger',
            [
                'uses'  => 'ReportController@export_employee_ledger',
                'as'    => 'report.export_employee_ledger'
            ]
        ); 
        Route::get('/get_employee_ledger',
            [
                'uses'  => 'ReportController@get_employee_ledger',
                'as'    => 'report.get_employee_ledger'
            ]
        );  
        Route::get('/search_employee',
            [
                'uses'  => 'ReportController@search_employee',
                'as'    => 'report.search_employee'
            ]
        );   
        Route::get('/get_by_location',
            [
                'uses'  => 'ReportController@get_by_location',
                'as'    => 'report.get_by_location'
            ]
        );  
        Route::get('/actual-inventory',
            [
                'uses'  => 'ReportController@actual_inventory',
                'as'    => 'report.actual_inventory'
            ]
        );
        Route::get('/generate_pdf_actual_inventory',
            [
                'uses'  => 'ReportController@generate_pdf_actual_inventory',
                'as'    => 'report.generate_pdf_actual_inventory'
            ]
        );
        Route::get('/export_actual_inventory',
            [
                'uses'  => 'ReportController@export_actual_inventory',
                'as'    => 'report.export_actual_inventory'
            ]
        );
        Route::get('/get_actual_inventory',
            [
                'uses'  => 'ReportController@get_actual_inventory',
                'as'    => 'report.get_actual_inventory'
            ]
        );
        Route::get('/daily-inventory',
            [
                'uses'  => 'ReportController@daily_inventory',
                'as'    => 'report.daily_inventory'
            ]
        );
        Route::get('/generate_pdf_daily_inventory',
            [
                'uses'  => 'ReportController@generate_pdf_daily_inventory',
                'as'    => 'report.generate_pdf_daily_inventory'
            ]
        );
        Route::get('/export_daily_inventory',
            [
                'uses'  => 'ReportController@export_daily_inventory',
                'as'    => 'report.export_daily_inventory'
            ]
        );   
        Route::get('/get_daily_inventory',
            [
                'uses'  => 'ReportController@get_daily_inventory',
                'as'    => 'report.get_daily_inventory'
            ]
        );
        Route::get('/department-office',
            [
                'uses'  => 'ReportController@office_assets',
                'as'    => 'report.office_assets'
            ]
        );
        Route::get('/generate_pdf_office_assets',
            [
                'uses'  => 'ReportController@generate_pdf_office_assets',
                'as'    => 'report.generate_pdf_office_assets'
            ]
        );
        Route::get('/export_office_assets',
            [
                'uses'  => 'ReportController@export_office_assets',
                'as'    => 'report.export_office_assets'
            ]
        );
        Route::get('/get_office_assets',
            [
                'uses'  => 'ReportController@get_office_assets',
                'as'    => 'report.get_office_assets'
            ]
        );
        Route::get('/category',
            [
                'uses'  => 'ReportController@category_assets',
                'as'    => 'report.category_assets'
            ]
        );
        Route::get('/generate_pdf_category_assets',
            [
                'uses'  => 'ReportController@generate_pdf_category_assets',
                'as'    => 'report.generate_pdf_category_assets'
            ]
        );
        Route::get('/export_category_assets',
            [
                'uses'  => 'ReportController@export_category_assets',
                'as'    => 'report.export_category_assets'
            ]
        ); 
        Route::get('/report/category_assets',
            [
                'uses'  => 'ReportController@get_category_assets',
                'as'    => 'report.get_category_assets'
            ]
        );    
        Route::get('/unlocated-items',
            [
                'uses'  => 'ReportController@unlocated_items',
                'as'    => 'report.unlocated_items'
            ]
        );  
        Route::get('/generate_pdf_unlocated_items',
            [
                'uses'  => 'ReportController@generate_pdf_unlocated_items',
                'as'    => 'report.generate_pdf_unlocated_items'
            ]
        );
        Route::get('/export_unlocated_items_report',
            [
                'uses'  => 'ReportController@export_unlocated_items_report',
                'as'    => 'report.export_unlocated_items_report'
            ]
        );
        Route::get('/get_unlocated_items',
            [
                'uses'  => 'ReportController@get_unlocated_items',
                'as'    => 'report.get_unlocated_items'
            ]
        );
        Route::get('/inventory-list',
            [
                'uses'  => 'ReportController@inventory_list',
                'as'    => 'report.inventory_list'
            ]
        ); 
        Route::get('generate_pdf_inventory_list',
            [
                'uses'  => 'ReportController@generate_pdf_inventory_list',
                'as'    => 'report.generate_pdf_inventory_list'
            ]
        );    
        Route::get('/export_inventory_list',
            [
                'uses'  => 'ReportController@export_inventory_list',
                'as'    => 'report.export_inventory_list'
            ]
        );
        Route::get('/get_inventory_list',
            [
                'uses'  => 'ReportController@get_inventory_list',
                'as'    => 'report.get_inventory_list'
            ]
        ); 
        Route::get('/uploaded-scan',
            [
                'uses'  => 'ReportController@uploaded_scan',
                'as'    => 'report.uploaded_scan'
            ]
        );
        Route::get('/get_uploaded_scan',
            [
                'uses'  => 'ReportController@get_uploaded_scan',
                'as'    => 'report.get_uploaded_scan'
            ]
        ); 
        Route::get('/print_asset_barcodes',
            [
                'uses'  => 'ReportController@print_asset_barcodes',
                'as'    => 'report.print_asset_barcodes'
            ]
        );
        Route::get('/print-asset-report',
            [
                'uses'  => 'ReportController@print_asset_report',
                'as'    => 'report.print_asset_report'
            ]
        );
        Route::get('/print-employee-ledger-report',
            [
                'uses'  => 'ReportController@print_employee_ledger_report',
                'as'    => 'report.print_employee_ledger_report'
            ]
        );
        Route::get('/print-daily-inventory-report',
            [
                'uses'  => 'ReportController@print_daily_inventory_report',
                'as'    => 'report.print_daily_inventory_report'
            ]
        );
        Route::get('/print-office-report',
            [
                'uses'  => 'ReportController@print_office_report',
                'as'    => 'report.print_office_report'
            ]
        );
        Route::get('/print-category-report',
            [
                'uses'  => 'ReportController@print_category_report',
                'as'    => 'report.print_category_report'
            ]
        );
        Route::get('/print-unlocated-items-report',
            [
                'uses'  => 'ReportController@print_unlocated_items_report',
                'as'    => 'report.print_unlocated_items_report'
            ]
        );
        Route::get('/print-inventory-list-report',
            [
                'uses'  => 'ReportController@print_inventory_list_report',
                'as'    => 'report.print_inventory_list_report'
            ]
        );
    });

    Route::group(['prefix'=>'barcode'], function(){
        Route::get('/upload-scanned-barcode',
            [
                'uses'  => 'UploadedDataController@upload_file',
                'as'    => 'uploaded_data.upload_file'
            ]
        ); 
        Route::get('/barcode-entry',
            [
                'uses'  => 'UploadedDataController@scan_barcode',
                'as'    => 'uploaded_data.scan_barcode'
            ]
        ); 
        Route::get('/get_uploaded_file',
            [
                'uses'  => 'UploadedDataController@get_uploaded_file',
                'as'    => 'uploaded_data.get_uploaded_file'
            ]
        ); 
        Route::get('/scanned-barcode-list',
            [
                'uses'  => 'UploadedDataController@scanned_barcode_list',
                'as'    => 'uploaded_data.scanned_barcode_list'
            ]
        );
        Route::get('/get_scanned_barcode_list',
            [
                'uses'  => 'UploadedDataController@get_scanned_barcode_list',
                'as'    => 'uploaded_data.get_scanned_barcode_list'
            ]
        );
        Route::get('/print-custom-barcode',
            [
                'uses'  => 'UploadedDataController@print_custom_barcode',
                'as'    => 'uploaded_data.print_custom_barcode'
            ]
        );
        Route::get('/print_preview_custom_barcode_ajax',
            [
                'uses'  => 'UploadedDataController@print_preview_custom_barcode_ajax',
                'as'    => 'uploaded_data.print_preview_custom_barcode_ajax'
            ]
        );
        Route::get('/print_preview_custom_barcode/{property_number}/{layout_type}',
            [
                'uses'  => 'UploadedDataController@print_preview_custom_barcode',
                'as'    => 'uploaded_data.print_preview_custom_barcode'
            ]
        );
    });


    Route::group(['prefix'=>'employee'], function(){
        Route::get('get_data',
            [
                'uses'  => 'EmployeeController@get_data',
                'as'    => 'employee.get_data'
            ]
        );    
        Route::post('/upload_new_photo/{slug_token}',
            [
                'uses'  => 'EmployeeController@upload_new_photo',
                'as'    => 'employee.upload_new_photo'
            ]
        );
    });


    // Place all the resources here...
    Route::resource('employee', 'EmployeeController');
    Route::resource('report', 'ReportController');
    Route::resource('uploaded_data', 'UploadedDataController');
    Route::resource('uploaded_data_details', 'UploadedDataDetailsController');
    Route::resource('user', 'UserController');    
    
    // Place all the override resources here
    Route::group(['prefix'=>'management'], function(){
        Route::get('accountable-employees',
            [
                'uses'  => 'EmployeeController@index',
                'as'    => 'employee.index'
            ]
        );
        Route::get('{slug_token}/accountable-employee',
            [
                'uses'  => 'EmployeeController@show',
                'as'    => 'employee.show'
            ]
        ); 
        Route::get('users',
            [
                'uses'  => 'UserController@index',
                'as'    => 'user.index'
            ]
        ); 
        Route::get('{slug_token}/user',
            [
                'uses'  => 'UserController@show',
                'as'    => 'user.show'
            ]
        ); 
        
    });

}); 
