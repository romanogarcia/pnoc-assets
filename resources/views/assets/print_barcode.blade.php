<html>
    <head>
        <title>Print Barcode - PNOC</title>
        <meta charset="utf-8">
        <!-- CUSTOM CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
        
        <style type="text/css">
            html, body{
                font-family: sans-serif;
            }
        </style>
    </head>
<body class="print-preview_container_global_enc">
@if($data->property_number != '' && $data->property_number != ' ')
<div class="no-print btn-print-container">
    <!-- <button onclick="window.location='{{route('uploaded_data.print_custom_barcode')}}'" type="button" title="Click to Back" class="btn-print btn-back">BACK</button> -->
    <button onclick="window.print();" type="button" title="Click to Print" class="btn-print">PRINT</button>
</div> 


<div class="paper-page paper-page-default">
    <!-- <div class="img"> -->
        <img src="{{asset('images/barcode-logo.gif')}}" class="barcode-logo"> <img class="barcode" src="data:image/png;base64,{!!DNS1D::getBarcodePNG($data->property_number, 'C128')!!}" alt="Barcode" height="38" width="180">
    <!-- </div> -->
    <div class="barcode-number">
        {{$data->property_number}}
    </div>
</div>
@endif

<script type="text/javascript">
 
</script>
</body>
</html>