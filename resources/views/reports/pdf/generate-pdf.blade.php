<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
    <title>PAYROLL</title>
    <style type="text/css">
        /* @import url('https://fonts.googleapis.com/css?family=Roboto&display=swap'); */
        body, body * {
            font-family: 'Roboto', sans-serif;
            font-size: 12px;
        }
        .company_info {
            margin-bottom: 20px;
        }
        .company_name,
        .company_address {
            text-decoration: underline;
            margin: 5px 15px;
        }
        .employee_info {
            margin: 0 15px;
        }
        .employee_address {
            width: 25%;
        }
        .title {
            margin-top: 55px;
            margin-bottom: 20px;
        }
        .sub-title {
            font-weight: 600;
            float: left;
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
            margin-top: 40px;
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
    <div class="content-wrapper">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <p><img src="{{ asset('images/bentach-big-1-1.png') }}" alt="LOGO" height="100px" style="margin-left: -40px; margin-top: -45px;"></p>
                    <div class="row">
                        <div class="company_info">
                            <div class="company_name">
                                {{ Bentaco Information Technology Services }}
                            </div>
                            <div class="company_address">
                                {{ $company->address }}
                            </div>
                        </div>
                    </div>

                    <div class="employee_info">
                        <div>{{ $payroll_data->gender = 'male' ? 'Mr.' : 'Ms.' }}</div>
                        <div>{{ $payroll_data->first_name . " " . $payroll_data->middle_name . " " . $payroll_data->last_name }}</div>
                        <div class="employee_address">{{ $payroll_data->address }}</div>
                        <div class="employee_address">{{ $payroll_data->zipcode . ' ' . $payroll_data->city }}</div>
                        <div class="">Philippines</div> <!-- this should have been dynamic -->
                    </div>


                    <div class="title">
                        <div class="row">
                            <div class="sub-title letter-spacing">PAYROLL</div>
                            <div class="sub-title">Salary from {{ $company->company_name }}</div>
                        </div>
                        <div style="clear:both;"></div>
                    </div>

                    <div class="table1">
                        <table class="table">
                            <tr>
                                <td class="thead" style="width:50%">Billing-Number</td>
                                <td class="thead" style="width:50%">Billing-Period</td>
                            </tr>
                            <tr>
                                <td>{{ $payroll_data->billing_number }}</td>
                                <td>
                                    {{ $payroll_data->period_from }} to {{ $payroll_data->period_to }}
                                </td>
                            </tr>
                        </table>
                    </div>





                    <div class="table2">
                        <table class="table">
                            <tr>
                                <td class="thead head1">Description</td>
                                <td class="thead head2" style="text-align:center">Quantity</td>
                                <td class="thead head2" style="text-align:right">Base</td>
                                <td class="thead head2" style="text-align:right">PHP</td>
                                <td class="thead head2" style="text-align:center">Total PHP</td>
                            </tr>
                            <tr>
                                <td>{{ $payroll_data->position != '' ? $payroll_data->position : 'Basic'}}</td>
                                <td class="tr">1</td>
                                <td class="tr">{{ number_format($payroll_data->gross, 2) }}</td>
                                <td class="tr">{{ number_format($payroll_data->gross, 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Food Allowance</td>
                                <td class="tr">1</td>
                                <td class="tr">{{ number_format($allowances['food_allowance'], 2) }}</td>
                                <td class="tr">{{ number_format($allowances['food_allowance'], 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Transportation Allowance</td>
                                <td class="tr">1</td>
                                <td class="tr">{{ number_format($allowances['transportation_allowance'], 2) }}</td>
                                <td class="tr">{{ number_format($allowances['transportation_allowance'], 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Overtime</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($overtime, 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Total Gross wage and Allowance</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($payroll_data->basic_pay + $payroll_data->total_deduction, 2) }}</td>
                            </tr>
                            <tr>
                                <td>SSS</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($sss['EE'] * -1, 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Philhealth</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($philhealth['EE'] * -1, 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Pagibig</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($pagibig['EE'] * -1, 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Tax</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($tax['witholding_tax'] * -1, 2) }}</td>
                                <td class="tr"></td>
                            </tr>
                            <tr>
                                <td>Total Deduction</td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr"></td>
                                <td class="tr">{{ number_format($payroll_data->total_deduction * -1, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="thead">Payout</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="thead tr">{{ number_format($payroll_data->netpay, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    <br>
                    <p>The Salary will sent at {{ date('d.m.Y', strtotime($payroll_data->payroll_date)) }}: 
                        @if(isset($payroll_data->bank_name) && $payroll_data->bank_name != '')
                            {{ $payroll_data->bank_name }} 
                        @endif
                        @if(isset($payroll_data->bank_account_number) && $payroll_data->bank_account_number != '')
                            , Account: {{ $payroll_data->bank_account_number }}
                        @endif

                        @if(!isset($payroll_data->bank_name) && !isset($payroll_data->bank_account_number))
                            No bank information available.
                        @endif
                    </p>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>
