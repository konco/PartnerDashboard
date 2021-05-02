@extends('layouts.theme')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/vendors/jquery-asColorPicker/css/asColorPicker.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')

  <div class="content-wrapper">
      <div class="row">
        
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="button-top">
                <div class="row">
                  
                    <div class="col-md-2">
                      <div class="form-group row">
                        <label class="col-form-label"><b>Status</b></label>
                        <div class="col-sm-9">
                          <select class="form-control" name="status" id="status">
                            @foreach($status as $key => $value)
                            <option value="{{$key}}">{{$value}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group row">
                        <label class="col-form-label"><b>Date from</b> </label>
                        <div class="col-sm-9">
                          <div class="input-group input-daterange d-flex align-items-center">
                            <input type="text" class="form-control" id="fromDate" name="fromDate" value="{{ date('m//d/Y') }}" autocomplete="off">
                            <div class="input-group-addon mx-4"><b>to</b></div>
                            <input type="text" class="form-control" id="toDate" name="toDate" value="{{ date('m//d/Y') }}" autocomplete="off">
                          </div>
                        </div>
                      </div>
                    </div>
                  
                </div>
              </div>
              <div class="button-top">
                <div class="d-flex justify-content-between flex-wrap">
                  <div class="d-flex align-items-end flex-wrap">
                    <button id="refresh-form" class="btn btn-primary float-left mt-4"><i class="mdi mdi-refresh mr-1"></i> Refresh</button> &nbsp;
                    <form method="post" action="{{ route('transactions.export') }}">
                      @csrf
                      <input type="hidden" name="exportStatus">
                      <input type="hidden" name="exportPartner">
                      <input type="hidden" name="exportFromDate">
                      <input type="hidden" name="exportEndDate">
                      <input type="hidden" name="exportSearch">
                      <button id="refresh-form" class="btn btn-info float-left mt-4" name="export"><i class="mdi mdi-folder-download mr-1"></i> Export</button>
                    </form>
                    
                  </div>
                  <div class="d-flex justify-content-between align-items-end flex-wrap">
                    <button id="filter-form" class="btn btn-success pull-right float-right mt-4"><i class="mdi mdi-magnify mr-1"></i>Filter</button>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="table-responsive">
                  <table class="table table-striped table-bordered datatablelist" id="data-table" width="100%">
                    <thead>
                      <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Trx_no</th>
                        <th>Brand Name</th>
                        <th>Product Name</th>
                        <th>Product Price</th>
                        <th>Reference No</th>
                        <th>Customer Data</th>
                        <th>Inquiry</th>
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

@endsection



@section('js')
<script src="{{ URL::asset('assets/vendors/jquery-asColorPicker/jquery-asColorPicker.min.js') }}"></script>
<script src="{{ URL::asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ URL::asset('assets/js/formpickers.js') }}"></script>

<script type="text/javascript">
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': "{{ csrf_token() }}"
    }
  });

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

  $('#refresh-form').on('click', function (e) {
    $('input[name="fromDate"]').val('');
    $('input[name="toDate"]').val('');
    $('select[name="status"]').val('');
    $('select[name="partner"]').val('');

      $("#data-table tbody").css('opacity','');
      e.preventDefault();
      oTable.draw();
  });

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
          url: "{!! route('transactions.query') !!}",
          method: "POST",
          data: function (d) {
              d.fromDate = $('input[name="fromDate"]').val();
              d.toDate = $('input[name="toDate"]').val();
              d.status = $('select[name="status"]').val();
              d.partner = $('select[name="partner"]').val();
              d.search = $('input[type="search"]').val();
          },
          error: function(d){
              console.error('UNKNOWN_ERROR');
          }
      },
      fixedColumns: true,
      columns: [
        { data: 'created_at', name: 'created_at' },
        { 
          data: null,
          render: function (data) {
              return getTrxStatusBtn(data.status)
          },
          orderable: false
        },
        { 
          data: null,
          render: function (data) {
              return getActionBtn(data.uuid, data.trx_no)
          },
          orderable: false
        },
        
        { data: 'brand_name', name: 'brand_name' },
        { data: 'product_name', name: 'product_name' },
        { data: 'product_price', name: 'product_price' },
        { data: 'reference_no', name: 'reference_no' },
        { data: 'customer_data', name: 'customer_data' },
        { 
          data: null,
          render: function (data) {
              return inquiry(data.uuid)
          },
          orderable: false
        }
          
      ]
  });

  $('button[name="export"]').on('click', function (e) {
    $('input[name="exportFromDate"]').val($('input[name="fromDate"]').val());
    $('input[name="exportEndDate"]').val($('input[name="toDate"]').val());
    $('input[name="exportStatus"]').val($('select[name="status"]').val());
    $('input[name="exportPartner"]').val($('select[name="partner"]').val());
    $('input[name="exportSearch"]').val($('input[type="search"]').val());
  });

});


function getActionBtn(uuid, trx_no) {
  var url = '{{ route("transactions.details", [":uuid"]) }}';
  url = url.replace(':uuid',uuid);
  var trxText = '';
  if(trx_no == '' || trx_no == null){
    trxText = 'Details';
  }else{
    trxText = trx_no;
  }
  btn_edit = '<a href="'+url+'">'+
      '<button type="button" class="btn btn-outline-info btn-icon-text">'+
        '<i class="mdi mdi-eye btn-icon-append"></i> ' +trxText+
      '</button> '+
    '</a>';

  return btn_edit;
}

function inquiry(uuid)
{
  let uuidString = "'"+uuid+"'";

  btn_edit = '<button type="button" class="btn btn-success btn-icon-text" id="inquiry-btn-'+uuid+'" onClick="sendInquiry('+uuidString+')">'+
        '<i class="mdi mdi-telegram btn-icon-append"></i>'+
        ' Inquiry '+
      '</button>';

  return btn_edit;
}

function sendInquiry(uuid)
{ 
  var url = '{{ route("transactions.inquiry", [":uuid"]) }}';
  url = url.replace(':uuid',uuid);

  $.ajax({
    type: "POST",
    url: url,
    dataType:"JSON",
    beforeSend: function() {
      $('#inquiry-btn-'+uuid).prop('disabled', true);
    },
    success: function(data){
      if(data.error == false){
        showSuccessMessage(data.message);
        $('#data-table').DataTable().draw(false);
        
      }else if(data.error == true){
        showDangerMessage(data.message);
      }
        
    },
    error: function(jqxhr) {
      if(jqxhr.responseJSON.error == true) { showDangerMessage(jqxhr.responseJSON.message); }
      $.each(jqxhr.responseJSON.errors, function (key, item){
          showDangerMessage(item);
      });
    },
    complete:function(data){
      $('#inquiry-btn-'+uuid).prop('disabled', false);
    }
  });
}

</script>

@endsection

