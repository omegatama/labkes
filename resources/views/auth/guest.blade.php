@extends('layouts.guest')

@section('titleBar', 'Selamat Datang')

@section('extraCss')
<style>
	img.center {
	    display: block;
	    margin: 0 auto;
	}
</style>
@endsection

@section('content')
	<div class="main-content">
    	<div class="content-wrapper">
        <!--Welcome Page Starts-->
        	<section id="login">
        		<div class="container-fluid">
            		<div class="row full-height-vh m-0">
            			<div class="col-12 d-flex align-items-center justify-content-center">
                			<div class="card">
                				<div class="card-header pb-2">
						        	<h4 class="card-title">Silahkan masuk sebagai:</h4>  
						        </div>
                				<div class="card-content">
                   					<div class="card-body login-img">
                    					<div class="row m-0">
                        					<div class="col-md-6 py-2 text-center align-middle">
                        						<a href="{{ route('login.admin') }}">
                        							<img src="app-assets/img/logo-kabsemarang.webp" alt="" class="img-fluid mt-1 center d-md-block d-none" width="200">
                        							Admin
                        						</a>
                        					</div>
                        					<div class="col-md-6 py-2 text-center align-middle">
                        						<a href="{{ route('login.sekolah') }}">
                        							<img src="app-assets/img/logo-sekolah.svg" alt="" class="img-fluid mt-1 center d-md-block d-none" width="280">
                        							Sekolah
                        						</a>
                        					</div>
                        					{{-- <div class="col-lg-6 col-md-12 bg-white px-4 pt-3">
					                        	
                							</div> --}}
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