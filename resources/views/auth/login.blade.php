@extends('layouts.master')
@section('title', 'Login - PNOC')
@section('customcss')
  <style>
    
  </style>
@endsection

@section('content')
<div class="content-wrapper d-flex align-items-center auth px-0">
    <div class="row w-100 mx-0">
        <div class="col-lg-4 mx-auto">
        
            @if (Session::has('userlock'))
            <div class="alert alert-danger alert-block text-center" >
            	<strong>{{ Session::get('userlock') }}</strong>
            </div>
            @endif
                    
            <div class="card">
<!--                <div class="card-header">Login</div> -->
                <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                    <div class="brand-logo">
                        <img src="{{@asset('images/barcode-logo.gif')}}" alt="logo">
                    </div>
                    <h6 class="font-weight-light">Sign in to continue.</h6>
                    <form class="pt-3" method="POST" action="{{ route('login') }}" autocomplete="off">
                        @csrf
                        
                        <?php $message ?>
                        
                        <div class="form-group">
                            <input id="username" type="text" name="username" class="form-control form-control-lg @error('username') is-invalid @enderror @error('email') is-invalid @enderror" placeholder="Username or Email" value="{{ old('username') }}" required autocomplete="off" autofocus>
                                
                            @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <input id="password" type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" required autocomplete="off">
                                
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <button id="btn-sign-in" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</button>
                        </div>
<!--                         <div class="my-2 d-flex justify-content-between align-items-center"> -->
<!--                             <div class="form-check"> -->
<!--                                 <label class="form-check-label text-muted"> -->
<!--                                 <input type="checkbox" class="form-check-input"> -->
<!--                                     Keep me signed in -->
<!--                                 <i class="input-helper"></i></label> -->
<!--                             </div> -->
<!--                             <a href="#" class="auth-link text-black">Forgot password?</a> -->
<!--                         </div> -->
                        <br>
                        <hr>
                       
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('customjs')
  <script>
$(document).ready(function()
	    {   
	        var element = $("#textbox").hide();
	        var count = 0;
	        $("#btn-sign-in").on("click", function()
	        {
		        
	            count++;//count button clicked
	            var elem = element.clone();
	            //then the element will be cloned same amount as button click, stuck here
	        });
	    });
  </script>
@endsection