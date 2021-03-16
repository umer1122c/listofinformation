@extends('front.template')
@section('content')
<!------ HEADER-END ------>
        <section class="course_bg">
            <div class="container">
                <div class="text-center">
                    <h5>Checkout</h5>
                    <h1>Details</h1>
                </div>
            </div>
        </section>
        <nav aria-label="breadcrumb" class="breadcrumbs-custom">
            <ol class="breadcrumb checkout_breadcrumbs ">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Mentorship Details</a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout Details</li>
            </ol>
        </nav>
        <section class="checkout_wrapper">
            <div class="container">
                <div class="main_head text-center mt-2">
                    <h2>Checkout</h2>
                </div>
                <div class="checkout_phr text-center">
                    <p>To proceed, please login if you already have an account or register to create your account. If
                        you are checking out, please then complete the process by making the payment.</p>
                </div>
            </div>
        </section>
        <section class="main_checkout">
            <div class="container-fluid">
                <div class="full_width">
                    <div class="row">
                        @if(session('user_id') == '')
                            <div class=" col-md-12 col-lg-6">
                                <div class="login_bg">
                                    <h3>Login</h3>
                                    <form class="login_form">
                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-4 col-form-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="l_email" id="l_email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Password</label>
                                            <div class="col-sm-8">
                                                <input type="password" name="l_password" id="l_password" class="form-control" id="inputPassword">
                                            </div>
                                        </div>
                                        <hr class="border_clr">
                                        <div class="login_btn">
                                            <button type="button" class="btn btn-blue loginUser">Submit</button>

                                        </div>
                                    </form>
                                </div>
                                <div class="reg_bg">
                                    <h3>Registration
                                    </h3>
                                    <h4>PROFILE INFORMATION:</h4>
                                    <hr class="black_clr">
                                    <form class="regi_form">

                                        <div class="form-group row">
                                            <label for="staticEmail" class="col-sm-4 col-form-label">Email</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="email" id="email" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Password</label>
                                            <div class="col-sm-8">
                                                <input type="password" name="password" id="password" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">First Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="first_name" id="first_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Last Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="last_name" id="last_name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row checkout_date">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Date</label>
                                            <div class="col-sm-8" >
                                                <input type="text" name="dob" id="datepicker"class="form-control"  id="datepicker">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Sex</label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="gender" id="gender">
                                                    <option value="Male">Male</option>
                                                    <option value="Female">Female</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Nationality</label>
                                            <div class="col-sm-8">
                                                <input type="text" name="nationality" id="nationality" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Mobile Contact
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" name="mobile" id="mobile" class="form-control" placeholder="(000) 000-0000">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Telephone Contact
                                            </label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="phone" id="phone" placeholder="(000) 000-0000">
                                            </div>
                                        </div>
                                        <h4>EDUCATION, TRAINING, SKILLS & EXPERIENCE:</h4>
                                        <hr class="black_clr">
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Education, Training, Skills & Experience
                                            </label>
                                            <div class="col-sm-8">
                                                <textarea rows="4" name="skills" id="skills" class="form-control" cols="50"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Why Join
                                            </label>
                                            <div class="col-sm-8">
                                                <textarea rows="4" name="why_join" id="why_join" class="form-control" cols="50"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputPassword" class="col-sm-4 col-form-label">Employment Status
                                            </label>
                                            <div class="col-sm-8">
                                                <select class="form-control" name="employee_status" id="employee_status">
                                                    <option value="Employed">Employed</option>
                                                    <option value="Not Employed">Not Employed</option>
                                                    <option value="Retired & Working">Retired & Working</option>
                                                    <option value="Retired & Not Working">Retired & Not Working</option>
                                                    <option value="Other">Other</option>
                                                  </select>
                                            </div>
                                        </div>
                                        <hr class="border_clr">
                                        <div class="login_btn">
                                            <button type="button" class="btn btn-blue signupUser">Submit</button>

                                        </div>
                                    </form>

                                </div>
                            </div>
                        @endif
                        <div class="col-md-12 col-lg-6">
                            <div class="order-table">
                                <h3>Cart</h3>
                                @if(count($items) > 0)
                                    @foreach($items as $row)
                                        <div class="row box-shadow mb-4 rowCount{{$row->rowId}}">
                                            <div class="col-4 col-sm-4 col-md-3 col-lg-3 pr-0">
                                                <div class="order-img">
                                                    <img class="shape " src="@if(file_exists(public_path('/courses/').$row->course_image)) {{$courseImageUrl.$row->course_image}} @else {{asset('front/placeholder.png')}} @endif">
                                                </div>
                                            </div>
                                            <div class="col-6 col-sm-6 col-md-7 col-lg-6 pl-0">
                                                <div class="order-text ">
                                                    <h4>{!!  commonHelper::textLimit($row->course_title , 40) !!}</h4>
                                                    <h5> <span class="black-clr">$ {{number_format($row->price , 2)}}</span></h5>
                                                    <div class="input-group quantity_btn">
                                                        <span class="input-group-prepend">
                                                            <button type="button" class="btn  btn-number" data-type="minus" onclick="decrease('{{$row->course_id}}' , '{{$row->cart_id}}')" data-field="quant[1]">
                                                                <span class="fa fa-minus"></span>
                                                            </button>
                                                        </span>
                                                        <input type="text" class="input-number qty count_{{$row->cart_id}}" value="{{$row->qty}}" min="1" max="1000">
                                                        <span class="input-group-append">
                                                            <button type="button" class="btn  btn-number" data-type="plus" onclick="increase('{{$row->course_id}}' , '{{$row->cart_id}}','{{$row->course_title}}','{{$row->price}}')" data-field="quant[1]">
                                                                <span class="fa fa-plus"></span>
                                                            </button>
                                                        </span>
                                                    </div>
<!--                                                    <div class="quantity buttons_added">
                                                        <input type="button" value="-" class="minus">
                                                        <input type="number" step="1" min="1" max="" name="quantity" value="{{$row->qty}}" title="Qty" class="input-text qty text" size="4" pattern="" inputmode="">
                                                        <input type="button" value="+" class="plus">
                                                    </div>-->
                                                </div>
                                            </div>
                                            <div class="col-2 col-sm-2 col-md-2 col-lg-3">
                                                <div class="cart_text">
                                                    <a href="javascript:void(0)" class="close-bg" onclick="deleteCartItem('{{$row->rowId}}')"><span class="close"><img src="{{asset('front/assets/images/close.png')}}"></span></a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12">
                                            <div class="cart-bottom">
                                                <div class="price-bg">
                                                    <strong class="sub-head">Subtotal</strong>
                                                    <strong class="price-head">$ <span class="price-total">{{number_format($total , 2)}}</span></strong>
                                                </div>
                                                <div class="price-bg">
                                                    <strong class="sub-head">Total</strong>
                                                    <strong class="price-head">$ <span class="price-total">{{number_format($total , 2)}}</span></strong>
                                                </div>
                                                <div class="price-btn">
                                                    <a href="javascript:void(0)"><button class="checkout_btn mb-4" disabled="">Proceed to Checkout</button></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row">
                                        <div class="col-md-12 col-lg-12"><p>Cart empty.</p></div>
                                    </div>
                                @endif
                              </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!---- FORGOT-PASSWORD-START ----->
    <div class="modal fade" id="removePayment" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Remove Product</h5>
                    <span type="text" class="close" data-dismiss="modal">&times;</span>
                </div>
                <div class="modal-body py-4">
                  <span>Are you sure you want to delete this item?</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-blue" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-blue" data-dismiss="modal" onclick="deleteCartItemConfirm()">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <!---- FORGOT-PASSWORD-END ----->
        <!------ FOOTER_SECTION_START ------>
@endsection