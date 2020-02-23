<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <title>Employee Ledger</title>
        <style type="text/css">
            /* @import url('https://fonts.googleapis.com/css?family=Roboto&display=swap'); */
            @page {
                margin: 150px 25px;
            }
            header {
                position: fixed;
                top: -120px;
                left: 0px;
                right: 0px;
                height: 110px;
            }
            body, body * {
                font-family: 'Roboto', sans-serif;
                font-size: 12px;
            }
            .company_name,
            .company_address {
                font-weight: bold;
                text-decoration: underline;
            }
            .employee_info {
                float: left;
                width: 100%;
            }
            /*.employee_address {
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

            .table2 {
                margin-top: 10px;
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
            .employee {
                margin-bottom: 10px;
            }   
            .employee-title {
                font-weight: 600;
                float: left;
                margin-right: 25px;
            }
            .id{
                margin-left: 50%;
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
                <div class="sub-title letter-spacing">EMPLOYEE LEDGER</div>
                <div class="sub-title1">{{ date('F m, Y')}}</div>
            </div>
            <div style="clear:both;"></div>
        </div>
        @if(isset($employee_data))
        <div class="employee">
            <div class="row">
                <div class="employee-title">Name:</div>
                <div class="employee-title">{{ ucwords($employee_data->first_name.' '.$employee_data->last_name) }}</div>
            </div>
            <div class="id">
                    <div class="employee-title">Employee No.:</div>
                    <div class="employee-title">{{ $employee_data->employee_id }}</div>
            </div>
            <div style="clear:both;"></div>
        </div>
        <div class="employee">
            <div class="row">
                <div class="employee-title">Office:</div>
                <div class="employee-title">{{ ucwords($employee_data->office) }}</div>
            </div>
        </div>
        @endif
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
                                    <th>Item Description</th>
                                    <th>Accounting Tag</th>
                                    <th>Property No.</th>
                                    <th>MR No.</th>
                                    <th>Location</th>
                                  </tr>
                                </thead>
    
                                <tbody>
                                  @foreach($data as $d)
                                    <tr>
                                      <td>{{ $d->item_description }}</td>
                                      <td>{{ $d->item_description }}</td>
                                      <td>{{ $d->property_number }}</td>
                                      <td>{{ $d->mr_number }}</td>
                                      <td>{{ $d->location }}</td>
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
        