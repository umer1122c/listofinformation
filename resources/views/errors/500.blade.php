@extends('errors.template')
@section('content')
    <!------ 404_SSECTION_START ------>
    <section class="error_wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="error-content">
                        <h2>
                            <strong>500</strong>
                        </h2>
                        <h3>Oops... Page Not Found!
                        </h3>
                        <p>Try using the button below to go to main page of the site
                        </p>
                        <a href="{{url('/')}}"><button class="blue_btn">Go to Home Page</button></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!------ 404_SSECTION_END ------>
@endsection 