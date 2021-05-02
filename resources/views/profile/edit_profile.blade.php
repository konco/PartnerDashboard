@extends('layouts.theme')

@section('content')

	<div class="content-wrapper">
      	
      	<div class="row">
	        <div class="col-lg-12">
	          <div class="card">
	            <div class="card-body">
	              <h4 class="card-title">User Data</h4>
	              <form class="cmxform" id="editProfile" method="post" action="{{ route('profile.update.profile') }}">
	              	@csrf
	                <fieldset>
	                  <div class="form-group">
	                    <label for="username">Username</label>
	                    <input id="username" class="form-control" name="username" type="text" value="{!! (!empty(auth()->user()->username)) ? auth()->user()->username : ""  !!}" disabled="disabled" readonly="readonly">
	                  </div>
	                  <div class="form-group">
	                    <label for="name">Name</label>
	                    <input id="name" class="form-control" name="name" type="text" value="{!! (!empty(auth()->user()->name)) ? auth()->user()->name : ""  !!}">
	                  </div>
	                  <div class="form-group">
	                    <label for="email">Email</label>
	                    <input id="email" class="form-control" name="email" type="email" value="{!! (!empty(auth()->user()->email)) ? auth()->user()->email : ""  !!}">
	                  </div>
	                  <div class="form-group">
	                    <label for="telegram">ID Telegram</label>
	                    <input id="telegram" class="form-control" name="telegram" type="text" value="{!! (!empty(auth()->user()->telegram)) ? auth()->user()->telegram : ""  !!}">
	                  </div>
	                  <div class="form-group">
	                    <label for="password">Password</label>
	                    <input id="password" class="form-control" name="password" type="password">
	                  </div>
	                  
	                  <input class="btn btn-primary" type="submit" value="Submit">
	                </fieldset>
	              </form>
	            </div>
	          </div>
	        </div>
      	</div>
    </div>

@endsection

@section('js')

	<script src="{{ URL::asset('assets/vendors/jquery-validation/jquery.validate.min.js') }}"></script>
  	<script src="{{ URL::asset('assets/vendors/jquery.avgrund/jquery.avgrund.min.js') }}"></script>
  	<script src="{{ URL::asset('assets/js/form-validation.js') }}"></script>
  	<script src="{{ URL::asset('assets/js/bt-maxLength.js') }}"></script>

  	<script src="{{ URL::asset('assets/vendors/sweetalert/sweetalert.min.js') }}"></script>
  	<script src="{{ URL::asset('assets/vendors/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
  	<script src="{{ URL::asset('assets/js/alerts.js') }}"></script>
  	<script src="{{ URL::asset('assets/js/avgrund.js') }}"></script>
  	<script src="{{ URL::asset('assets/js/bulgad.js') }}"></script>

@endsection