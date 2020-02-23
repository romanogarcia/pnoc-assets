<html>
    <head>
        <title>Print Custom Barcode - PNOC</title>
        <meta charset="utf-8">
        <!-- <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}"> -->

        <!-- CUSTOM CSS -->
        <link rel="stylesheet" type="text/css" href="{{ asset('css/custom.css') }}">
        
        <style type="text/css">
            html, body{
                font-family: sans-serif;
            }
        </style>
    </head>
<body class="print-preview_container_global_enc">


<div class="no-print btn-print-container">
    <button onclick="window.print();" type="button" title="Click to Print" class="btn-print">PRINT</button>
</div> 

@if($layout_type == 'default')
    <div class="paper-page paper-page-default">
        <img src="{{asset('images/barcode-logo.gif')}}" class="barcode-logo"> <img class="barcode" src="data:image/png;base64,{!!DNS1D::getBarcodePNG($property_numbers[0], 'C128')!!}" alt="Barcode" height="38" width="180">
        <div class="barcode-number">
            {{$property_numbers[0]}}
        </div>
    </div>
@endif

@if($layout_type == '6_small_barcode')
    <div class="paper-page">
        @foreach($property_numbers as $property_number_key => $property_number)
            <div class="layout-sm-6">
                <div class="text-center">
                    @if($property_number != '' && strlen($property_number) != 0)
                        <img src="{{asset('images/barcode-logo.gif')}}" class="barcode-logo"> 
                        <img class="barcode" src="data:image/png;base64,{!!DNS1D::getBarcodePNG($property_number, 'C128')!!}" alt="Barcode">
                        <div class="barcode-number">
                            {{$property_number}}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif
@if($layout_type == '2_onehalf_2_small')
    <div class="paper-page">
        @foreach($property_numbers as $property_number_key => $property_number)
            <div class="{{($property_number_key == 0 || $property_number_key == 2) ? 'layout-sm-4-l':'layout-sm-4-r'}}">
                <div class="text-center">
                    @if($property_number != '' && strlen($property_number) != 0)
                        <img src="{{asset('images/barcode-logo.gif')}}" class="barcode-logo"> 
                        <img class="barcode" src="data:image/png;base64,{!!DNS1D::getBarcodePNG($property_number, 'C128')!!}" alt="Barcode">
                        <div class="barcode-number">
                            {{$property_number}}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif


<script type="text/javascript">
 
</script>
</body>
</html>