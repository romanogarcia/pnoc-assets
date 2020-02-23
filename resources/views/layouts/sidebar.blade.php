            <nav class="sidebar sidebar-offcanvas" id="sidebar">
               <ul class="nav">
                  <li class="nav-item">
                     <a class="nav-link" href="{{ route('home') }}">
                     <i class="mdi mdi-home menu-icon"></i>
                     <span class="menu-title">Dashboard</span>
                     </a>
                  </li>
                  @if(Utility::get_user_module_access() || Utility::get_employee_module_access())
                  <li class="nav-item">
                     <a class="nav-link" data-toggle="collapse" href="#sidebar-management_tab" aria-expanded="false" aria-controls="sidebar-management_tab">
                     <i class="mdi mdi-account-multiple menu-icon"></i>
                     <span class="menu-title">Management</span>
                     <i class="menu-arrow"></i>
                     </a>
                     <div class="collapse" id="sidebar-management_tab">
                        <ul class="nav flex-column sub-menu">
                           @if(Utility::get_employee_module_access())
                           <li class="nav-item"> <a class="nav-link" href="{{ route('employee.index') }}"> <i class="mdi mdi-account-multiple"></i>&nbsp; &nbsp; Accountable Employees</a></li>
                           @endif
                           @if(Utility::get_user_module_access())
                           <li class="nav-item"> <a class="nav-link" href="{{ route('user.index') }}"> <i class="mdi mdi-account-settings"></i>&nbsp; &nbsp; Users</a></li>
                           @endif
                        </ul>
                     </div>
                  </li>
                  @endif
                  <li class="nav-item">
                     <a class="nav-link" data-toggle="collapse" href="#sidebar-reports_tab" aria-expanded="false" aria-controls="sidebar-reports_tab">
                     <i class="mdi mdi-file-multiple menu-icon"></i>
                     <span class="menu-title">Report</span>
                     <i class="menu-arrow"></i>
                     </a>
                     <div class="collapse" id="sidebar-reports_tab">
                        <ul class="nav flex-column sub-menu">
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.assets')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Assets</a></li>
                           <!-- <li class="nav-item"> <a class="nav-link" href="{{route('report.memorandum_receipt')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Memorandum Receipt</a></li> -->
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.employee_ledger')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp; Employee Ledger Card</a></li>
                           <!-- <li class="nav-item"> <a class="nav-link" href="{{route('report.actual_inventory')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Actual Inventory</a></li> -->
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.daily_inventory')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp; Daily Inventory</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.office_assets')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Department/Office Assets</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.category_assets')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp; Category Assets</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.unlocated_items')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Unlocated Items</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{route('report.inventory_list')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Inventory List</a></li>
                           <!-- <li class="nav-item"> <a class="nav-link" href="{{route('report.uploaded_scan')}}"><i class="mdi mdi-table"></i>&nbsp;&nbsp;  Uploaded Scanned Barcode</a></li> -->
                        </ul>
                     </div>
                  </li>
                  @if(Utility::get_barcode_module_access())
                  <li class="nav-item">
                     <a class="nav-link" data-toggle="collapse" href="#sidebar-barcode_tab" aria-expanded="false" aria-controls="sidebar-barcode_tab">
                     <i class="mdi mdi-barcode menu-icon"></i>
                     <span class="menu-title">Barcode</span>
                     <i class="menu-arrow"></i>
                     </a>
                     <div class="collapse" id="sidebar-barcode_tab">
                        <ul class="nav flex-column sub-menu">
                           <li class="nav-item"> <a class="nav-link" href="{{ route('uploaded_data.scan_barcode') }}"> <i class="mdi mdi-barcode-scan"></i>&nbsp; &nbsp; Barcode Entry</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{ route('uploaded_data.upload_file') }}"> <i class="mdi mdi-cloud-upload"></i>&nbsp; &nbsp; Upload Scanned Barcode</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{ route('uploaded_data.scanned_barcode_list') }}"> <i class="mdi mdi-barcode"></i>&nbsp; &nbsp; Scanned Barcode List</a></li>
                           <li class="nav-item"> <a class="nav-link" href="{{ route('uploaded_data.print_custom_barcode') }}"> <i class="mdi mdi-cloud-print"></i>&nbsp; &nbsp; Print Custom Barcode</a></li>
                        </ul>
                     </div>
                  </li>
                  @endif
                  <li class="nav-item">
                     <a class="nav-link" target="_blank" href="{{route('home.Documentation')}}">
                     <i class="mdi mdi-file-document menu-icon"></i>
                     <span class="menu-title"> Documentation</span>
                     </a>
                  </li>
               </ul>

            </nav>