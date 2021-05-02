@extends('layouts.theme')

@section('content')

  <div class="content-wrapper">
      <div class="row">
        
        <div class="col-lg-12 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <div class="button-top">
                <div class="row">
                  <div class="col-lg-12">
                    <h4 class="card-title float-left">{{ $title }}</h4>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Product Category</b></h4>
                        <ul class="nav nav-pills nav-pills-success" id="pills-tab" role="tablist">
                          @foreach($productCategory as $value)
                            <li class="nav-item">
                              <a class="nav-link" id="pills-{{ $value->code }}-tab" data-toggle="pill" href="#" role="tab" aria-controls="pills-{{ $value->code }}" aria-selected="false" onClick="selectCategory('{{ $value->code }}')">{{ $value->code }}</a>
                            </li>
                          @endforeach
                        </ul>
                    </div>
                  </div>

                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Product Brand</b></h4>
                        <div class="col-lg-12">
                          <div class="form-group row" id="brand-list">

                          </div>
                        </div>

                    </div>
                  </div>

                  <div class="card">
                    <div class="card-body">
                      <h4 class="card-title">Product Denomination</b></h4>
                        <div class="col-lg-12">
                          <div class="form-group row" >
                            <form class="cmxform" id="product-denom">
                              <div id="field-denom"></div>

                              <div  class="form-group row" id="denom-list"></div>
                             
                            </form>
                            
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
<script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

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
      heading: 'Error',
      text: "{{ session('error') }}",
      showHideTransition: 'slide',
      icon: 'error',
      loaderBg: '#f2a654',
      position: 'top-right'
    })
@endif


function selectCategory(code)
{
  $('#field-denom').html('');
  $('#denom-list').html('');
  $('#brand-list').html('');
  var url = '{{ route("topup.getBrands") }}';
  $.ajax({
    type: "POST",
    url: url,
    data:{code:code},
    dataType:"JSON",
    beforeSend: function() {
      $('#filter-category'+code).prop('disabled', true);
    },
    success: function(data){
      if(data.error == false){
        var brandData = data.category;
        brandData = JSON.parse(brandData);

        if(brandData.length > 0){
          var brandDatataList = '';
          for(let i = 0; i < brandData.length; i++){
            var brand_code = "'"+brandData[i].brand_code+"'";
            var brand_name = "'"+brandData[i].brand_name+"'";

            brandDatataList += '<ul class="nav nav-pills nav-pills-custom" id="pills-tab-custom" role="tablist">'+
                              '<li class="nav-item">'+
                                '<a class="nav-link" id="pills-'+brandData[i].brand_code+'-tab-custom" data-toggle="pill" href="#" onClick="selectBrand('+brand_code+', '+brand_name+')" role="tab" aria-controls="pills-'+brandData[i].brand_code+'" aria-selected="false">'+brandData[i].brand_name+'</a>'+
                              '</li>'+
                              '</ul>';
          }
          $('#brand-list').append(brandDatataList);
        }
        
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
      $('#filter-category'+code).prop('disabled', false);
    }
  });
}

function selectBrand(code, brand_name)
{
  $("#pills-tab-custom>li>a.active").removeClass("active");
  document.getElementById('pills-'+code+'-tab-custom').classList.add("active");

  $('#field-denom').html('');
  $('#denom-list').html('');
  var url = '{{ route("topup.getProductList") }}';
  var brand_name = "'"+brand_name+"'";

  $.ajax({
    type: "POST",
    url: url,
    data:{code:code},
    dataType:"JSON",
    beforeSend: function() {
      $('#pills-'+code+'-tab-custom').prop('disabled', true);
    },
    success: function(data){
      if(data.error == false){
        var productField = data.field;
        productField = JSON.parse(productField);
        var productData = data.list;
        productData = JSON.parse(productData);

        if(productField !== null){
          if(productField.length > 0){
            var productFieldList = '';
            for(let i = 0; i < productField.length; i++){
              productFieldList += '<div class="form-group">'+
                                '<label for="name">'+capitalize(productField[i].name)+'</label>'+
                                '<input id="'+productField[i].name+'" class="form-control" name="'+productField[i].name+'" autocomplete="off" type="text">'+
                                '</div>';
            }
            $('#field-denom').append(productFieldList);
          }  
        }
        

        if(productData.length > 0){
          var productDataList = '';
          for(let i = 0; i < productData.length; i++){
            var product_code = "'"+productData[i].product_code+"'";
            var product_price = "'"+productData[i].product_price+"'";
            var product_name = "'"+productData[i].product_name+"'";
            

            productDataList += '<ul class="nav nav-pills nav-pills-custom" id="pills-tab-customs" role="tablist">'+
                              '<li class="nav-item">'+
                                '<a class="nav-link" id="pills-'+productData[i].product_code+'-tab-customs" data-toggle="pill" href="#" onClick="selectDenom('+product_code+', '+product_price+', '+brand_name+', '+product_name+')" role="tab" aria-controls="pills-'+productData[i].product_code+'" aria-selected="false">'+productData[i].product_name+'<br>('+formatRupiah(productData[i].product_price)+')</a>'+
                              '</li>'+
                              '</ul>';
          }
          $('#denom-list').append(productDataList);
        }
        
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
      $('#pills-'+code+'-tab-custom').prop('disabled', false);
    }
  });
}

function selectDenom(product_code, product_price, brand_name, product_name){
  $("#pills-tab-customs>li>a.active").removeClass("active");
  document.getElementById('pills-'+product_code+'-tab-customs').classList.add("active");
  var formData = $( "#product-denom" ).serializeArray();

  Swal.fire({
    title: 'Apakah kamu yakin?',
    text: "Transaksi ini tidak dapat dibatalkan!",
    icon: 'warning',
    input: 'password',
    inputPlaceholder: 'Input Key',
    //inputClass: 'form-control',
    inputAttributes: {
      //autocapitalize: 'off'
    },
    //showCancelButton: true,
    confirmButtonText: 'Submit',
    confirmButtonColor: '#3f51b5',
    cancelButtonColor: '#ff4081',
    showLoaderOnConfirm: true,
    preConfirm: (key) => {
      if(key){
        return new Promise(function (resolve) {
          $.ajax({
              url: '{{ route("topup.createTransaction") }}',
              data: $.param({ 'product_code': product_code, 'product_price':product_price, 'brand_name':brand_name, 'product_name':product_name, 'key':key, 'field': formData }),
              method: "POST",
              success: function(data){
                if(data.error == false){
                  var iconText = 'question'
                  if(data.trx_status == 'SUCCESS'){
                    var iconText = 'success'
                  }else if(data.trx_status == 'PENDING'){
                    var iconText = 'info'
                  }else if(data.trx_status == 'FAIL'){
                    var iconText = 'warning'
                  }
                  Swal.fire({
                    title: data.message,
                    icon: iconText,
                    html: '<div class="card card-inverse-secondary">'+
                            '<div class="card-body">'+
                              '<p id="clipboardExample3" style="text-align:left;">'+
                                'Transaction number : <b>'+data.trx_no+'</b><br>'+
                                'Brand name : <b>'+data.brand_name+'</b><br>'+
                                'Product name : <b>'+data.product_name+'</b><br>'+
                                'Price : <b>'+data.price+'</b><br>'+
                                'Reference number : <b>'+data.reference_no+'</b><br>'+
                              '</p>'+
                            '</div>'+
                          '</div>',
                  })
                  
                }else if(data.error == true){
                  Swal.showValidationMessage(
                    `Error : ${data.message}`
                  )
                  Swal.hideLoading();
                }
              },
              error: function(jqxhr) {
                if(jqxhr.responseJSON.error == true) { showDangerMessage(jqxhr.responseJSON.message); }
                $.each(jqxhr.responseJSON.errors, function (key, item){
                   
                    Swal.showValidationMessage(
                      `Error : ${item}`
                    )
                    Swal.hideLoading();
                });
              }
          })
          
        });
      }else{
        Swal.showValidationMessage(
          '<i class="fa fa-info-circle"></i> Your key is required'
        )
      }
      
    },
    allowOutsideClick: () => !Swal.isLoading()
  })
  
}

function capitalize(string) {
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}

</script>

@endsection

