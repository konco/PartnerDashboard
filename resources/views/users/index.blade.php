@extends('layouts.theme')

@section('content')

	<div class="content-wrapper">
      <div class="row">
        
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="button-top">
                <div class="d-flex justify-content-between flex-wrap">
                  <div class="d-flex align-items-end flex-wrap">
                    <button id="filter-form" class="btn btn-primary float-left mt-4"><i class="mdi mdi-refresh mr-1"></i> Refresh</button>
                  </div>
                  <div class="d-flex justify-content-between align-items-end flex-wrap">
                    
                    <a href="{{ route('users.create') }}" class="btn btn-success float-right mt-4"><i class="mdi mdi-plus-circle mr-1"></i>Add User</a>
                    
                  </div>
                </div>
              </div>

              
              @if(session('success'))
                <div class="alert alert-success" role="alert">
                  <p>{{session('success')}}</p>
                </div>
              @endif

              @if(count($errors) > 0)
                <div class="alert alert-danger" role="alert">
                  <ul>
                      @foreach($errors->all() as $error)
                          <li>{{$error}}</li>
                      @endforeach
                  </ul>
                </div>
              @endif
              
              <div class="table-responsive">
                <table class="table table-hover" id="data-table">
                  <thead>
                    <tr>
                      <th>Username</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Last Login at</th>
                      <th>Last Login IP</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  
                </table>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>

@endsection



@section('js')

<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
  });

@if(session('error'))
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
  $('#filter-form').on('click', function (e) {
      $("#data-table tbody").css('opacity','');
      e.preventDefault();
      oTable.draw();
  });

  var oTable = $('#data-table').DataTable({
      responsive: true,
      processing: true,
      serverSide: true,
      pageLength: 50,
      autoWidth: true,
      ajax: {
          url: "{!! route('users.query') !!}",
          method: "POST",
          data: function (d) {
              d.search = $('input[type="search"]').val();
          },
          error: function(d){
              console.error('UNKNOWN_ERROR');
          }
      },
      fixedColumns: true,
      columns: [
          { data: 'username', name: 'username' },
          { data: 'name', name: 'name' },
          { data: 'email', name: 'email' },
          { data: 'last_login_at', name: 'last_login_at',orderable: false },
          { data: 'last_login_ip', name: 'last_login_ip',orderable: false },
          { 
            data: null,
            render: function (data) {
                return getActionBtn(data.link)
            },
            orderable: false
          }
      ]
  });

});

function getActionBtn(link) {
  btn_edit = '<a href="'+link+'">'+
      '<button type="button" class="btn btn-outline-secondary btn-icon-text">'+
        '<i class="mdi mdi-pencil btn-icon-append"></i>'+
        'Edit'+
      '</button> '+
    '</a>';

  return btn_edit;
}

</script>

@endsection

