@extends('front.template')
@section('content')
<!------ HEADER-END ------>
<section class="course_bg">
    <div class="container">
        <div class="text-center">
            <h5>Courses</h5>
            <h1>Details</h1>
        </div>
    </div>
</section>
<nav aria-label="breadcrumb" class="breadcrumbs-custom_details ">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item"><a href="#">Mentorship Details</a></li>
        <li class="breadcrumb-item active" aria-current="page">Courses Details</li>
    </ol>
</nav>
<section class="courses_detail">
    <div class="container">
        <div class="main_head text-center mt-2">
            <h2>Nural Networking Course</h2>
        </div>
        <div class="row">
            <div class="col-md-8 col-lg-8">
                <div class="course_img">
                    <img src="assets/images/course_img.png" alt="course_img">
                </div>
            </div>
            <div class="col-md-4 col-lg-4">
                <div class="price_list">
                    <ul class="list-unstyled">
                        <li class="blue_text">
                            <span class="float-left">Price</span>
                            <span class="float-right">$420</span>
                        </li><br>
                        <li>
                            <span class="float-left">Start</span>
                            <span class="float-right">07/08/ 2020</span>
                        </li><br>
                        <li>
                            <span class="float-left">End</span>
                            <span class="float-right">08/08/ 2020</span>
                        </li><br>
                        <li>
                            <span class="float-left">Event Category</span>
                            <span class="float-right">Course</span>
                        </li>
                        <br>
                        <li>
                            <span class="float-left">Total Slot</span>
                            <span class="float-right">100</span>
                        </li><br>
                        <li>
                            <span class="float-left">Booked Slot</span>
                            <span class="float-right">00</span>
                        </li><br>
                        <li>
                            <span class="float-left">Website</span>
                            <span class="float-right">http://globaboom.com</span>
                        </li>
                    </ul>
                    <div class="price_btn">
                        <button class="add_btn">Add to cart</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-lg-8">
                <div class="courses_head">
                    <h3>Course Description </h3>
                </div>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas When an unknown printer took a galley of type and scrambled it to make a type specimen book. It has svived not only five centuries, but also the leap into electronic typesetting, remaining essentially.Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas When an unknown printer took a galley of type and scrambled it to make a type specimen book. It has svived not only five centuries, but also the leap into electronic typesetting, remaining essentially.
                </p>
            </div>
        </div>
    </div>
</section>
@endsection 
 
