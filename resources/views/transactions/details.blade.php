@extends('layouts.theme')

@section('content')

	<div class="content-wrapper">
      	
      	<div class="row">
	        <div class="col-lg-12">
	          <div class="card">
	            <div class="card-body">
	            	<h4 class="card-title">Transaction Details</h4>

	            	<div class="row">
	            		<div class="col-md-6">
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Transaction number : </b> {{ $transaction->trx_no }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Transaction Status : </b> <label class="badge badge-{{$badge_status}}">{{ $transaction->status }}</label></label>
	            			</div>
	            			
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Reference no : </b> {{ $transaction->reference_no }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Product Price : </b> {{ number_format($transaction->price, 2, ',', '.') }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Product Name : </b> {{ $transaction->product_name }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Product Brand : </b> {{ $transaction->brand_name }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Data from User : </b></label>
	            			</div>
	            			<div class="col-md-12">
	            				<div class="card card-inverse-secondary">
			                        <div class="card-body">
			                          <p id="clipboardExample3">
			                            @foreach($customer_data as $key => $value)
	            							{{ $key }} => {{ $value }} <br>
	            						@endforeach
			                          </p>
			                        </div>
			                      </div>
	            			</div>

	            		</div>	

	            		<div class="col-md-6">
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Transaction created_at : </b> {{ $transaction->created_at }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Transaction updated_at : </b> {{ $transaction->updated_at }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Transaction created_ip : </b> {{ $transaction->created_ip }}</label>
	            			</div>
	            			<div class="col-md-12">
	            				<label class="col-form-label"><b>Response Message : </b> </label>
	            			</div>
	            			<div class="col-md-12">
	            				<div class="card card-inverse-secondary">
			                        <div class="card-body">
			                          <p id="clipboardExample3">
			                            {{ $transaction->response }}
			                          </p>
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

	
@endsection