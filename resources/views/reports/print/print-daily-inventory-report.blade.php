<!DOCTYPE html>
<html>
    <head>
        <title>Daily Inventory Report - Print</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
        <!-- CUSTOM CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
        <style type="text/css" media="print">
            @page { 
                size: landscape !important;
            }
        </style>
    </head>
<body>
<div class="print-report-container">


    <div class="no-print btn-print-container">
        <button onclick="window.print();" type="button" title="Click to Print" class="btn-print">PRINT</button>
    </div> 

    <h2 class="text-center">DAILY INVENTORY REPORT</h2>

    <div class="row">
        @foreach($filter as $f_key => $f_value)
            @if($f_value != '')
            <div class="col-3">
                <b>{{$f_key}}:</b> {{$f_value}}
            </div>
            @endif
        @endforeach
    </div>

    <b>Date: </b> {{date("Y-m-d")}}
    <br><br>
    <table class="table print-table-report">
        <thead> 
            <tr>
                <th>Asset No.</th>
                <th>Property No.</th>
                <th>Item Description</th>
                <th>Acquisition Cost</th>
                <th>Current Location</th>
                <th>Original Location</th>
                <th>Employee</th>
                <th>PO No.</th>
                <th>Supplier</th>
                <th>Date Acquired</th>
                <th>Date Added</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_amount   = 0;
                $total_count    = 0;
            @endphp
            @foreach($data as $row)
            <tr>
                <td>{{$row->asset_number}}</td>
                <td>{{$row->property_number}}</td>
                <td>{{$row->item_description}}</td>
                <td>{{number_format($row->acquisition_cost,2)}}</td>
                <td>{{$row->current_location_name}}</td>
                <td>{{$row->original_location_name}}</td>
                <td>{{ucwords($row->first_name.' '.$row->last_name)}}</td>
                <td>{{$row->po_number}}</td>
                <td>{{$row->supplier_name}}</td>
                <td>{{$row->date_acquired}}</td>
                <td>{{date('Y-m-d',strtotime($row->created_at))}}</td>
            </tr>
            @php
                $total_amount   += $row->acquisition_cost;
                $total_count++;
            @endphp
            @endforeach
        </tbody>
    </table> 
    <hr>
    <div class="text-right">
        <b>Number of Asset:</b> {{$total_count}}
        <br>
        <b>Total Amount:</b> {{number_format($total_amount,2)}}
    </div>
</div>

</body>
</html>