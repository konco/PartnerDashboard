@extends('layouts.theme')

@section('content')

	<div class="content-wrapper">
      	<div class="row">
	        <div class="col-12">
	          <div class="card">
	            <div class="card-body">
	              <div class="row">
	                <div class="col-lg-3">
	                  <div class="border-bottom text-center pb-4">
	                    <img src="{{ URL::asset('assets/images/faces/default.png') }}" alt="{{ Auth::user()->name }}" class="img-lg rounded-circle mb-3"/>
	                    <div class="mb-3">
	                      <h3>{{ Auth::user()->name }}</h3>
	                      <div class="d-flex align-items-center justify-content-center">
	                        <h5 class="mb-0 mr-2 text-muted"><i class="flag-icon flag-icon-id" title="id" id="id"></i> Indonesia </h5>
	                      </div>
	                    </div>
	                    
	                  </div>
	                  
	                  <div class="py-4">
	                    <p class="clearfix">
	                      <span class="float-left">
	                        Name
	                      </span>
	                      <span class="float-right text-muted">
	                        {{ Auth::user()->name }}
	                      </span>
	                    </p>
	                    <p class="clearfix">
	                      <span class="float-left">
	                        Username
	                      </span>
	                      <span class="float-right text-muted">
	                        {{ Auth::user()->username }}
	                      </span>
	                    </p>
	                    <p class="clearfix">
	                      <span class="float-left">
	                        Status
	                      </span>
	                      <span class="float-right text-muted">
	                        Active
	                      </span>
	                    </p>
	                    <p class="clearfix">
	                      <span class="float-left">
	                        Phone
	                      </span>
	                      <span class="float-right text-muted">
	                        -
	                      </span>
	                    </p>
	                    <p class="clearfix">
	                      <span class="float-left">
	                        Mail
	                      </span>
	                      <span class="float-right text-muted">
	                        {{ Auth::user()->email }}
	                      </span>
	                    </p>
	                  </div>
	                  <a href="{{ route('profile.edit.profile') }}"><button class="btn btn-primary btn-block mb-2">Edit</button></a>
	                </div>
	                <div class="col-lg-9">
	                  	<div class="mt-4 py-2 border-top border-bottom">
	                    	<ul class="nav profile-navbar">
		                      	<li class="nav-item">
		                        	<a class="nav-link" id="loginhistory-tab" href="#loginhistory" data-toggle="tab">
			                          	<i class="mdi mdi-newspaper"></i>
			                          	Login History
		                        	</a>
		                      	</li>
	                    	</ul>
	                  	</div>
	                  		<div class="profile-feed">
	                    		<div class="tab-content">
			                    	<div class="tab-pane fade show active" id="loginhistory" role="tabpanel" aria-labelledby="loginhistory-tab">
			                    		<div class="col-md-12">
			                    			<div class="table-responsive">
							                <table class="table table-hover" id="loginhistory-table" width="100%">
							                  <thead>
							                    <tr>
							                    	<th></th>
							                      	<th>Time</th>
							                      	<th>Type</th>
							                      	<th>Name</th>
							                      	{{-- <th>Additional Data</th> --}}
							                      	<th>Status</th>
							                      	<th>Message</th>
							                    </tr>
							                  </thead>
							                  
							                </table>
							              </div>
			                    		</div>
				                      	
			                    	</div>
				                    
		                  		</div>
	                  		</div>
	                	</div>
	              	</div>
	            </div>
	          </div>
	        </div>
	    </div>
    </div>

@endsection



@section('js')

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
@elseif(session('error'))
	$.toast({
      heading: 'Danger',
      text: "{{ session('error') }}",
      showHideTransition: 'slide',
      icon: 'error',
      loaderBg: '#f2a654',
      position: 'top-right'
    })
@endif

$(function() {
    $('#loginhistory-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{!! route('profile.query.login') !!}',
        "order": [[ 0, "desc" ]],

        columns: [
            { data: 'last_login_at', name: 'last_login_at' },
            { data: 'ip', name: 'ip',orderable: false },
            { data: 'platform', name: 'platform',orderable: false },
            { data: 'browser', name: 'browser',orderable: false },
            { data: 'device', name: 'device',orderable: false },
            { data: 'region', name: 'region',orderable: false },
            
        ]
    });

});

function details(d){
    return '<div class="card card-inverse-secondary">' +
      	'<div class="card-body">' +
	        '<p>' +
	          '<b>Additional Data:</b> ' +d.additional_data + ''+
	        '</p>' +
      	'</div>' +
    '</div>';  
}

</script>

@endsection

