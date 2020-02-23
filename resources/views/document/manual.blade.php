@extends('layouts.master')
@section('title', 'PNOC Manual Documentation')
@section('customcss')
  <style> </style>
@endsection

@section('content')
   <div class="content-wrapper">
          	
<iframe src="{{@asset('document/manual.pdf') }}" style="width:600px; height:500px;" frameborder="0"> </iframe>
   </div>
@endsection

@section('customjs')
  <script></script>
@endsection