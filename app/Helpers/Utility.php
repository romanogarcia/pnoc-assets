<?php 

namespace App\Helpers;
use Illuminate\Support\Facades\File;
use App\User;
use App\UserRole;
use App\Employee;
use App\Department;
use App\Location;
use App\Supplier;
use App\Category;
use App\Role;
use Auth;
class Utility
{
    
    public static function generate_unique_token($lenght = 13) {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("no cryptographically secure random function available");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }

    public static function create_new_upload_dir($new_folder){
        //Public directory/folder
        $root = public_path();

        //If uploads folder not exists, create new
        $new_uploads_folder = "uploads";
        if (!File::exists($root.'/'.$new_uploads_folder)) {
            File::makeDirectory($root.'/'.$new_uploads_folder);
        }

        // If employees folder not exist in uploads folder create new
        if (!File::exists($root.'/'.$new_uploads_folder.'/'.$new_folder)) {
            File::makeDirectory($root.'/'.$new_uploads_folder.'/'.$new_folder);
        }

        return $root.'/'.$new_uploads_folder.'/'.$new_folder.'/';
    }

    public static function get_employee_photo($photo){

        $default_photo = asset('images/default-user.png');
        $get_photo_public = public_path('uploads/employees/'.$photo);
        $get_photo = asset('uploads/employees/'.$photo);

        if(file_exists($get_photo_public) && $photo != '')
            return $get_photo;
        else
            return $default_photo;    
    }

    public static function account_login_attempt($username_key, $username_value, $password){
        $user = User::where($username_key, $username_value)->first();
              
        if($user){
            $modelRole = UserRole::where('user_id', $user->id)->first();
            if( $modelRole->role_id == '1'){ // exempt admin
                return true;
            }
            
            
            if($user->is_locked == 1)
                \Session::flash('userlock', 'Your account has been locked');
            
            if(password_verify($password, $user->password) && $user->is_locked == '0'){
                $user->login_attempts = 0;
                $user->save();

                return true;
            }else{
                $maxAttempts = 3;
                $current_attempt = $user->login_attempts+1;
                $user->login_attempts = $current_attempt;
                
                if($current_attempt >= $maxAttempts){
                    $user->is_locked = 1;
                    
                    \Session::flash('userlock', 'Your account has been locked');
                }
        
                $user->save();
                
                return false;
            }
        }else{
            return false;
        }
    }

    public static function current_employee_id(){
        $current_user_id = auth()->user()->id;
        $current_emp = Employee::where('user_id', $current_user_id)->first(array('id'));
        return $current_emp->id; //get the employee id of the current user
    }

    public static function get_file_contents($path){
        return File::get($path);
    }

    public static function get_formatted_file_size($bytes){
        if ($bytes >= 1073741824){
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576){
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024){
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1){
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1){
            $bytes = $bytes . ' byte';
        }
        else{
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    public static function validate_department($requested_department){

        if(is_numeric($requested_department)){
            $get_department                     = Department::where('id', $requested_department)->first();
        }else{
            $get_department                     = false;
        }

        // Check if exist the input
        if($get_department){
            $department_id          = $requested_department; //department id
        }else{ //if not exist create new
            
            $get_department         = Department::where('name', $requested_department)->first();

            if($get_department){
                $department_id      =  $get_department->id; //department id
            }else{
                $department         = new Department();
                $department->name   = ucfirst($requested_department);
                $department->save();
                $department_id      =  $department->id; //department id
            }
        }

        return $department_id;
    }

    public static function validate_location($requested_location){

        if(is_numeric($requested_location)){
            $get_location                   = Location::where('id', $requested_location)->first();
        }else{
            $get_location                   = false;
        }

        // Check if exist the input
        if($get_location){
            $location_id                = $requested_location;
        }else{ //if not exist create new

            $get_location               = Location::where('branch_name', $requested_location)->first();

            if($get_location){
                $location_id            =  $get_location->id; //location id
            }else{
                $location               = new Location();
                $location->branch_name  = ucfirst($requested_location);
                $location->save();
                $location_id            =  $location->id;
            }
        }

        return $location_id;
    }

    public static function validate_supplier($requested_supplier){

        if(is_numeric($requested_supplier)){
            $get_supplier                   = Supplier::where('id', $requested_supplier)->first();
        }else{
            $get_supplier                     = false;
        }

        // Check if exist the input
        if($get_supplier){
            $supplier_id                = $requested_supplier;
        }else{ //if not exist create new
            $get_supplier               = Supplier::where('name', $requested_supplier)->first();
            if($get_supplier){
                $supplier_id            =  $get_supplier->id; //location id
            }else{
                $supplier               = new Supplier();
                $supplier->name         = ucfirst($requested_supplier);
                $supplier->save();
                $supplier_id            =  $supplier->id;
            }
        }

        return $supplier_id;
    }
    
    public static function validate_category($requested_category){
        
        if(is_numeric($requested_category)){
            $get_category               = Category::where('id', $requested_category)->first();
        }else{
            $get_category                     = false;
        }

        // Check if exist the input
        if($get_category){
            $category_id            = $requested_category;
        }else{ //if not exist create new
            $get_category           = Category::where('name', $requested_category)->first();
            
            if($get_category){
                $category_id        = $get_category->id;
            }else{
                $category           = new Category();
                $category->name     = ucfirst($requested_category);
                $category->save();
                $category_id        =  $category->id;
            }

        }

        return $category_id;
    }

    public static function get_current_user_photo(){
        $user_id = auth()->user()->id;
        $employee = Employee::where('user_id', $user_id)->first();
        if($employee){
            return Utility::get_employee_photo($employee->photo);
        }else{
            return Utility::get_employee_photo('');
        }
    }

    public static function get_date_format($date_acquired, $output_format='F d, Y', $datatables=true){
        if($datatables){
            if($date_acquired != '0000-00-00'){
                $date           = date($output_format, strtotime($date_acquired));
                $date_sort      = date("Y-m-d", strtotime($date_acquired));
                $response       = '<span data-description="'.$date_sort.'">'.$date.'</span>';
            }else{
                $response       = '';
            }
        }else{
            $response           = date($output_format, strtotime($date_acquired));
        }

        
        return $response;
    }

    public static function get_date_added_format($date_added, $output_format='F d, Y', $datatables=true){
        $date           = date($output_format, strtotime($date_added));
        $date_sort      = date("Y-m-d", strtotime($date_added));
        if($datatables){
            $response   = '<span data-description="'.$date_sort.'">'.$date.'</span>';
        }else{
            $response   = $date;
        }

        return $response;
    }
    
    public static function get_user_module_access(){
        $current_user_id    = auth()->user()->id;
        $get_role           = UserRole::where('user_id', $current_user_id)->first();
        $allowed            = array('1');

        if(in_array($get_role->role_id, $allowed))
            $response = true;
        else
            $response = false;
        
        return $response;
    }

    public static function get_employee_module_access(){
        $current_user_id    = auth()->user()->id;
        $get_role           = UserRole::where('user_id', $current_user_id)->first();
        $allowed            = array('1');

        if(in_array($get_role->role_id, $allowed))
            $response = true;
        else
            $response = false;
        
        return $response;
    }

    public static function get_barcode_module_access(){
        $current_user_id    = auth()->user()->id;
        $get_role           = UserRole::where('user_id', $current_user_id)->first();
        $allowed            = array('1');

        if(in_array($get_role->role_id, $allowed))
            $response = true;
        else
            $response = false;
        
        return $response;
    }

    public static function get_current_role(){
        $modelRole = UserRole::where('user_id', auth()->user()->id)->first();
        return $modelRole->role_id;
    }

    public static function get_server_datetime($time_zone='Asia/Manila', $format="F d, Y H:i:s"){
        if($time_zone != 'default')
            date_default_timezone_set($time_zone);
        
        return date($format, time());
    }

}