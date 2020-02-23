@extends('layouts.master')
@section('title', 'PNOC Manual Documentation')
@section('customcss')
  <style> </style>
@endsection

@section('content')
<div class="content-wrapper">
	<embed src="{{ @asset('documentation/manual.pdf') }}" width="1200px" height="1000px" />
</div>
@endsection

@section('customjs')
  <script></script>
@endsection