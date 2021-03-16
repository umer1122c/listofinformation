<!------ HEADER-START ------>
<?php $header_categories = commonHelper::getCategories(); ?>
<header>
    <div class="navigation-wrap  start-header start-style">
        <div class="container-fluid">
            <div class="full_width">
                <div class="min_logo">
                    <a class="mobile_view" href="{{url('/')}}">
                        <img src="{{asset('front/assets/images/brand-204x36.png')}}" alt="">
                    </a>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="main_header_area animated">
                            <nav class=" navigation navbar navbar-expand-lg navbar-light" id="navigation1">
                                <a class="navbar-brand web_view" href="{{url('/')}}">
                                    <img src="{{asset('front/assets/images/brand-204x36.png')}}" alt="">
                                </a>
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse " id="navbarSupportedContent">
                                        <ul class="navbar-nav py-4 py-md-0 nav-menu align-to-right">
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('membership')}}">MEMBERSHIP CATEGORIES</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('programme')}}">PROGRAMMES (SERVICES)</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="javascript:void(0)">ABOUT US</a>
                                                <ul class="nav-dropdown">
                                                    <li class="nav-item">
                                                        <a href="{{url('history')}}">History</a>
                                                    </li>
                                                   <li class="nav-item">
                                                        <a href="{{url('mvstatements')}}">Mission & Vision Statements</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{url('organization')}}">Organization</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="nav-item">
                                                 <a class="nav-link" href="javascript:void(0)">COURSES</a>
                                                 <ul class="nav-dropdown">
                                                     <li class="nav-item">
                                                        <a  href="{{url('/cources/all')}}">All Course</a>
                                                     </li>
                                                     @if(count($header_categories) > 0)
                                                         @foreach($header_categories as $head_row)
                                                             <li class="nav-item">
                                                                 <a  href="{{url('course/category/'.$head_row->slug)}}">{!!  commonHelper::textLimit($head_row->title , 30) !!}</a>
                                                             </li>
                                                         @endforeach
                                                     @endif
                                                 </ul>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="{{url('/checkout')}}">CART (<span class="cartCount">{!!  commonHelper::getCartCount() !!}</span>)</a>
                                            </li>
                                            @if(session('user_id') != '')
                                                <li class="nav-item">
                                                    <a class="nav-link" href="{{url('/logout')}}">Logout</a>
                                                </li>
                                            @endif
                                        </ul>
                               </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!------ HEADER-END ------>