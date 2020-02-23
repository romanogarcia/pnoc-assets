<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Department;
use App\Supplier;
use App\Location;
use App\Employee;
use Response;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $departments = Department::orderBy('name', 'asc')->get();
        $suppliers = Supplier::orderBy('name', 'asc')->get();
        $locations = Location::orderBy('branch_name', 'asc')->get();
        $employees = Employee::orderBy('first_name', 'asc')->where('is_active', 1)->orderBy('last_name', 'asc')->get();
        
        return view('home', compact('categories', 'departments', 'suppliers', 'employees', 'locations'));
    }

    public function Documentation(){
        $filename = 'documentation/manual.pdf';
        $path = public_path($filename);

        return Response::make(file_get_contents($path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$filename.'"'
        ]);
    }
}
