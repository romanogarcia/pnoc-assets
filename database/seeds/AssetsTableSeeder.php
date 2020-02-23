<?php

use Illuminate\Database\Seeder;
use App\Asset;
use App\Category;
use App\Department;
use App\Location;
use App\Supplier;
use App\Employee;
use App\Helpers\Utility;
use Maatwebsite\Excel\ExcelServiceProvider;

class AssetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    
  
    
    public function run()
    {
        $path = public_path('partial-assets/partial-assets-1.xlsx');
        $data = Excel::load($path)->get();
        if($data->count() > 0){
            $tr = 0;
            // dd($data->toArray());
            foreach($data->toArray() as $key => $row){
                if($row['office'] != ''){
                    $tr++;
                    $office                 = $row['office'];

                    $department             = $row['department'];
                    if($department == '' || strlen($department) <= 2)
                        $department         = 'No Department';
                    $department_id          = Utility::validate_department($department);

                    $category               = $row['category'];
                    if($category == '' || strlen($category) <= 2)
                        $category = 'OTHER ASSET';
                    $category_id            = Utility::validate_category($category);

                    $supplier_contractor     = $row['supplier_contractor'];
                    if($supplier_contractor == '' || $supplier_contractor == ' ' || strlen($supplier_contractor) <= 2)
                        $supplier_contractor = 'No Supplier';
                    $supplier_id            = Utility::validate_supplier($supplier_contractor);

                    $inventory_report       = $row['inventory_report'];
                    $location               = $row['inventory_report']; //location
                    if($location == '' || $location == ' ' || strlen($location) <= 2)
                        $location = 'NO LOCATION';
                    $location_id            = Utility::validate_location($location);

                    $accountable_employee   = $row['accountable_employee'];
                    if( strpos($accountable_employee, ',') !== false ) {
                        $accountable_employee   = explode(',', $accountable_employee);
                        $ae_first_name          = $accountable_employee[1];
                        $ae_last_name           = $accountable_employee[0];
                    }else if(strpos($accountable_employee, ' ') !== false){
                        $accountable_employee   = explode(' ', $accountable_employee);
                        $ae_first_name          = $accountable_employee[1];
                        $ae_last_name           = $accountable_employee[0];
                        if(isset($accountable_employee[2])){
                            $ae_first_name .= ' '.$accountable_employee[2];
                        }
                    }else{
                        $ae_first_name          = $accountable_employee;
                        $ae_last_name           = $accountable_employee;
                    }
                    $accountable_employee   = $ae_first_name.' '.$ae_last_name;
                    $ae_last_name           = str_replace( ',', '', $ae_last_name );
                    $ae_first_name          = str_replace( ',', '', $ae_first_name );
                    $ae_last_name           = str_replace( '.', '', $ae_last_name );
                    $ae_first_name          = str_replace( '.', '', $ae_first_name );

                    $accountable_employee   = str_replace( ',', '', $accountable_employee );
                    if(!$ae_first_name){
                        $ae_first_name  = 'Missing';
                        $ae_last_name   = 'Missing';
                    }

                    $ae_first_name          = ltrim($ae_first_name);
                    $ae_last_name           = ltrim($ae_last_name);
                    $accountable_employee   = ltrim($accountable_employee);

                    $to_select_employee     = array('id');
                    $get_employee               = Employee::select($to_select_employee)
                                                    ->where('first_name', $ae_first_name)
                                                    ->where('last_name', $ae_last_name)
                                                    ->first();
                    if($get_employee){
                        $accountable_employee_id = $get_employee->id;                        
                    }else{
                        $new_employee                   = new Employee();
                        $new_employee->first_name       = $ae_first_name;
                        $new_employee->last_name        = $ae_last_name;
                        $new_employee->is_active        = 1;
                        $new_employee->slug_token       = Utility::generate_unique_token();
                        $new_employee->employee_no      = 'none_'.$tr;
                        $new_employee->department_id    = $department_id;
                        $new_employee->save();
                        $accountable_employee_id = $new_employee->id;
                    }

                    if($row['date_acquired'])
                        $date_acquired          = date("Y-m-d", strtotime($row['date_acquired']));
                    else
                        $date_acquired          = null;


                    $acquisition_cost       = $row['acquisition_cost'];
                    if($acquisition_cost == '')
                        $acquisition_cost   = 0;
                    else if($acquisition_cost == '-')
                        $acquisition_cost   = 0;
                    else if(!is_numeric($acquisition_cost))
                        $acquisition_cost   = 0;

                    $asset_tag_no           = $row['asset_tag_no.'];
                    $item_description       = $row['item_description'];
                    $property_number        = $row['property_number'];
                    $mr_no                  = $row['mr_no.'];
                    $po_or_invoice          = $row['po_or_invoice'];
                    $warranty               = $row['warranty'];
                    $remarks                = $row['remarks'];

                    /*Insert data*/
                    $asset = new Asset();
                    
                    // foreign keys
                    $asset->accountable_employee_id = $accountable_employee_id;
                    $asset->category_id             = $category_id;
                    $asset->department_id           = $department_id;
                    $asset->supplier_id             = $supplier_id;
                    $asset->location_id             = $location_id;
                    $asset->added_by                = 1; //default to admin
                    
                    //mandatory fields 
                    $asset->item_description        = trim($item_description);
                    $asset->acquisition_cost        = trim($acquisition_cost);
                    $asset->asset_number            = trim($asset_tag_no);
                    $asset->po_number               = trim($po_or_invoice);
                    $asset->mr_number               = trim($mr_no);
                    $asset->warranty                = trim($warranty);
                    $asset->report_of_waste_material= null;
                    $asset->disposal_number         = null;
                    $asset->serial_number           = null;
                    $asset->property_number         = trim($property_number);
                    $asset->acquisition_date        = trim($date_acquired);
                    $asset->condition               = null;
                    $asset->accounting_tag          = null;
                    $asset->slug_token              = Utility::generate_unique_token();
                    $asset->save();
                }
            }
        }
        
    }
    
    function randomDate($sStartDate, $sEndDate, $sFormat = 'Y-m-d H:i:s')
    {
        // Convert the supplied date to timestamp
        $fMin = strtotime($sStartDate);
        $fMax = strtotime($sEndDate);
        // Generate a random number from the start and end dates
        $fVal = mt_rand($fMin, $fMax);
        // Convert back to the specified date format
        return date($sFormat, $fVal);
    }
    
}
