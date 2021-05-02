@extends('layouts.theme')

@section('content')

	<div class="content-wrapper">
      	
      	<div class="row">
	        <div class="col-lg-9">
	          <div class="card">
	            <div class="card-body">
	              <h4 class="card-title">User Data</h4>
	              @if(session()->get('errors'))
	              	<div class="alert alert-danger" role="alert">
	                  <ul>
	                      @foreach($errors->all() as $error)
	                          <li>{{$error}}</li>
	                      @endforeach
	                  </ul>
	                </div>
	              @endif

	              @if(session('error'))
	                <div class="alert alert-danger" role="alert">
	                  {{ session('error') }}
	                </div>
	              @endif

	              <form class="cmxform" id="editProfile" method="post" action="{{ route('users.update',['id'=>$data->id]) }}">
	              	@csrf
	                <fieldset>
	                  
	                  <div class="form-group">
	                    <label for="username">Username</label>
	                    <input id="username" class="form-control" name="username" disabled="disabled" readonly="readonly" type="text" value="{!! (!empty($data->username)) ? $data->username : ""  !!}">
	                  </div>
	                  <div class="form-group">
	                    <label for="name">Name</label>
	                    <input id="name" class="form-control" name="name" type="text" value="{!! (!empty($data->name)) ? $data->name : ""  !!}">
	                  </div>
	                  <div class="form-group">
	                    <label for="email">Email</label>
	                    <input id="email" class="form-control" name="email" type="text" value="{!! (!empty($data->email)) ? $data->email : ""  !!}">
	                  </div>
	                  
	                  <div class="form-group">
	                    <label for="password">Password</label>
	                    <input id="password" class="form-control" name="password" type="password">
	                  </div>

	                  <div class="form-group">
	                    <label for="pin">PIN</label>
	                    <input id="pin" class="form-control" name="pin" type="password">
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

  	<script type="text/javascript">
  		@if(session('success'))
		    $.toast({
		      heading: 'Success',
		      text: "{{ session('success') }}",
		      showHideTransition: 'slide',
		      icon: 'success',
		      loaderBg: '#f96868',
		      position: 'top-right'
		    })
		
		@endif
  	</script>

@endsection