<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ URL::asset('assets/vendors/css/vendor.bundle.base.css') }}">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ URL::asset('assets/css/style.css') }}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo-icon.png') }}" />
  {!! NoCaptcha::renderJs() !!}
</head>

<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              {{-- <div class="brand-logo">
                <img src="{{ URL::asset('assets/images/logo.png') }}" alt="logo">
              </div> --}}
              <h6 class="font-weight-light">Sign in to continue.</h6>
              <form class="pt-3" method="POST" action="{{ route('login') }}" id="commentForm">
                @csrf
                <div class="form-group">
                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Username" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <label id="cname-error" class="error mt-2 text-danger" for="email">{{ $message }}</label>
                    @enderror
                </div>
                <div class="form-group">
                  <input type="password" class="form-control form-control-lg" id="password" placeholder="Password" name="password" required autocomplete="current-password">
                    @error('password')
                        <label id="password-error" class="error mt-2 text-danger" for="password">{{ $message }}</label>
                    @enderror
                    @if ($errors->has('password'))
                      <span class="help-block">
                          <label id="password-error" class="error mt-2 text-danger" for="password">{{ $errors->first('password') }}</label>
                      </span>
                  @endif
                </div>

                <div class="mt-3">
                  <button class="btn btn-block btn-primary btn-lg font-weight-medium" type="submit">SIGN IN</button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                      {{ __('Remember Me') }}
                    </label>
                  </div>
                  
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="{{ URL::asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="{{ URL::asset('assets/vendors/jquery-validation/jquery.validate.min.js') }}"></script>
  <script src="{{ URL::asset('assets/vendors/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="{{ URL::asset('assets/js/off-canvas.js') }}"></script>
  <script src="{{ URL::asset('assets/js/hoverable-collapse.js') }}"></script>
  <script src="{{ URL::asset('assets/js/template.js') }}"></script>
  <script src="{{ URL::asset('assets/js/settings.js') }}"></script>
  <script src="{{ URL::asset('assets/js/todolist.js') }}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{ URL::asset('assets/js/form-validation.js') }}"></script>
  <script src="{{ URL::asset('assets/js/bt-maxLength.js') }}"></script>
  <!-- End custom js for this page-->

</body>

</html>
