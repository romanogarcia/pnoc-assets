<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <title>Office Assets</title>
    <style type="text/css">
        /* @import url('https://fonts.googleapis.com/css?family=Roboto&display=swap'); */
        @page {
            margin: 100px 25px;
        }
        header {
            position: fixed;
            top: -70px;
            left: 0px;
            right: 0px;
            height: 60px;
        }
        body, body * {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
        }
        /* .company_info {
            margin-bottom: 20px;
        } */
        .company_name,
        .company_address {
            font-weight: bold;
            text-decoration: underline;
        }
        /* .employee_info {
            margin: 0 15px;
        }
        .employee_address {
            width: 25%;
        } */
        .title {
            margin-bottom: 20px;
        }
        .sub-title {
            font-weight: 600;
            margin-right: 25px;
        }
        .sub-title1 {
            margin-top: 10px;
            font-weight: 400;
            margin-right: 25px;
            margin-bottom: 10px;
        }
        .letter-spacing {
            letter-spacing: 5px;
        }
        .table {
            width: 100%;
        }
        table {
            border-collapse: collapse;
        }
        table td {
            border: 1px solid rgba(0,0,0,0.8);
            padding: 1px 7px;
        }
        .thead {
            font-weight: 600;
        }
        
        .table2 .head1{
            width: 52%;
        }
        .table2 .head2{
            width: 12%;
        }
        .tr {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        
    </style>
</head>
<body>
    <header>
        <div class="row">
            <div class="company_info">
                <div class="company_name">
                    PHILIPPINE NATIONAL OIL COMPANY
                </div>
            </div>
        </div>
        <div class="title">
            <div class="row">
                <div class="sub-title letter-spacing">OFFICE ASSETS</div>
                <div class="sub-title1">{{ date('F m, Y')}}</div>
            </div>
            <div style="clear:both;"></div>
        </div>
    </header>
    <main>
    <div class="content-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    
                    

                    <div class="table2">
                        <table class="table">
                            <thead class="thead">
                              <tr>
                                <th>Property No.</th>
                                <th>Asset No.</th>
                                <th>Item Description</th>
                                <th>Employee</th>
                                <th>Date Acquired</th>
                                <th>Location</th>
                                <th>Acquisition Cost</th>
                              </tr>
                            </thead>

                            <tbody>
                              @foreach($data as $d)
                                <tr>
                                  <td>{{ $d->property_number }}</td>
                                  <td>{{ $d->asset_number }}</td>
                                  <td>{{ $d->item_description }}</td>
                                  <td>{{ ucwords($d->first_name.' '.$d->last_name) }}</td>
                                  <td>{{ $d->date_acquired }}</td>
                                  <td>{{ $d->location }}</td>
                                  <td>{{ number_format($d->acquisition_cost, 2) }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>    
                    <div align="right">   
                            Number of Assets {{ $data->count() }}
                    </div>                
                </div>
            </div>
        </div>
    </div>
    </main>
</body>
</html>
