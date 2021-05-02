@extends('layouts.theme')

@section('content')

<div class="content-wrapper">

          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between flex-wrap">
                <div class="d-flex align-items-end flex-wrap">
                  
                  
                </div>
                
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body dashboard-tabs p-0">
                  <ul class="nav nav-tabs px-4 border-left-0 border-top-0 border-right-0" role="tablist">
                    <li class="nav-item">
                      <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab" aria-controls="overview" aria-selected="true">Info</a>
                    </li>
                  </ul>
                  <div class="tab-content py-0 px-0 border-left-0 border-bottom-0 border-right-0">
                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                      <div class="d-flex flex-wrap justify-content-xl-between">
                        
                        <div class="d-flex border-md-right flex-grow-1 align-items-left justify-content-left justify-content-md-left p-3 item">
                          <div class="icon-box-secondary mr-3">
                            <i class="mdi mdi-currency-usd"></i>
                          </div>
                          <div class="d-flex flex-column justify-content-around">
                            <small class="mb-1 text-muted">Saldo</small>
                            <h5 class="mr-2 mb-0">Rp {{ number_format($balance, 2, ',', '.') }}</h5>
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