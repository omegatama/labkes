@extends('layouts.guest')

@section('titleBar', 'Login')

@section('content')
    <div class="main-content">
        <div class="content-wrapper">
        <!--Welcome Page Starts-->
            <section id="login">
                <div class="container-fluid">
                    <div class="row full-height-vh m-0">
                        <div class="col-12 d-flex align-items-center justify-content-center">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body login-img">
                                        <div class="row m-0">
                                            <div class="col-lg-6 d-lg-block d-none py-2 text-center align-middle">
                                                @if(isset($url))
                                                    @if ($url == "admin")
                                                        <img src="{{ asset('app-assets/img/logo-kabsemarang.webp') }}" alt="" class="img-fluid mt-2 ml-3 mr-3" width="230">
                                                    @elseif ($url == "sekolah")
                                                        <img src="{{ asset('app-assets/img/logo-sekolah.svg') }}" alt="" class="img-fluid mt-2 mb-2" width="280">
                                                    @else
                                                        <img src="{{ asset('app-assets/img/gallery/login.png') }}" alt="" class="img-fluid mt-5" width="400" height="230">
                                                    @endif
                                                @else
                                                    <img src="{{ asset('app-assets/img/gallery/login.png') }}" alt="" class="img-fluid mt-5" width="400" height="230">
                                                @endif
                                            </div>
                                            <div class="col-lg-6 col-md-12 bg-white px-4 pt-3">
                                                <h4 class="mb-2 card-title">{{ __('Login') }} {{ isset($url) ? ucwords($url) : ""}}</h4>
                                                <p class="card-text mb-3">
                                                    Selamat datang kembali, silahkan login dahulu.
                                                </p>

                                                @isset($url)
                                                <form method="POST" action='{{ url("login/$url") }}' aria-label="{{ __('Login') }}">
                                                @else
                                                <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                                                @endisset
                                                    @csrf

                                                    <input type="text" id="email" name="email" class="form-control mb-1" placeholder="Email atau {{ ($url=='sekolah') ? 'NPSN' : 'Username' }}" />
                                                    <input type="password" id="password" name="password" class="form-control mb-1" placeholder="Password" />
                                                    <input type="number" id="ta" name="ta" class="form-control mb-1" placeholder="Tahun Anggaran" value="{{ date('Y') }}" />
                                                    
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <div class="remember-me">
                                                            <div class="custom-control custom-checkbox custom-control-inline mb-2">
                                                                <input type="checkbox" id="customCheckboxInline1" name="remember" class="custom-control-input" />
                                                                <label class="custom-control-label" for="customCheckboxInline1" style="text-transform: none; font-weight: inherit;">
                                                                  Remember Me
                                                                </label>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="forgot-password-option">
                                                            <a href="forgot-password-page.html" class="text-decoration-none text-primary">Forgot Password?</a>
                                                        </div> --}}
                                                    </div>
                                                    <div class="fg-actions d-flex justify-content-between">
                                                        {{-- <div class="login-btn">
                                                          <button class="btn btn-outline-primary">
                                                            <a href="register-page.html" class="text-decoration-none">Register</a>
                                                          </button>
                                                        </div> --}}
                                                        <div class="recover-pass">
                                                            <button type="submit" class="btn btn-primary">
                                                                Login
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                {{-- <hr class="m-0"> --}}
                                                {{-- <div class="d-flex justify-content-between mt-3 mb-3">
                                                    <h6 class="text-decoration-none text-primary">Belum punya akun?</h6>
                                                    <a href="register-page.html" class="text-decoration-none">Daftar Di sini!</a>
                                                </div> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--Welcome Page Ends-->
        </div>
    </div>
@endsection
