<!----- HEADER-CSS-START ------>
    <?php $header_categories = commonHelper::getCategories(); ?>
    <header>
        <div class="topmenu-bg">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-md-6 col-lg-4">
                        <div class="online_sports">
                            <p>
                                <span>
                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                </span>
                                ONLINE SUPPORT 24/7
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 search-bg">
                        <!--<form class="navbar-form " role="search">-->
                            <div class="input-group">
                                <input type="hidden" id="slug" name="slug" value="">
                                <input type="text" class="form-control search-box press-btn-search" placeholder="Search">
                                <div class="input-group-append">
                                    <button class="btn" onclick="headerSearch()" type="button">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="search-dropdown result"></div>
                        <!--</form>-->
                    </div>
                    @if(Session::get('user_id') == '')
                        <div class="col-6 col-md-6 col-lg-4">
                            <ul class="list-unstyled topmenu-btn">
                                <li class="d-inline-block"><a href="{{url('signup')}}">Sign Up</a> | </li>
                                <li class="d-inline-block"><a href="{{url('login')}}">Login</a></li>
                            </ul>
                        </div>
                    @else
                        <div class="col-6 col-md-6 col-lg-4">
                            <ul class="list-unstyled top-cart mb-0">
                                <li class="d-inline-block pr-2"> 
                                    <div class="profile-menu ">
                                        <a class="nav-link dropdown-toggle " data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{session('first_name')}}</a>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="{{ url('/my/account') }}">Profile</a>
                                            <a class="dropdown-item" href="{{ url('/my/orders') }}">My Order</a>
                                            <a class="dropdown-item" href="{{ url('/logout') }}">Logout</a>
                                        </div>
                                    </div>
                                </li>
                                <li class="cart-bg d-inline-block">
                                    <a href="{{ url('my/cart')}}">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        <span class="fcart-text cartCount">{!!  commonHelper::getCartCount() !!}</span>
                                    </a>
                               </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="navigation-wrap  start-header start-style ">
                <div class="container">
                    <div class="row">
                        <div class="col-4 col-sm-3 col-md-3">
                            <a class="navbar-brand" href="{{url('/')}}"> <img class="img-fluid b-logo" src="{{asset('front/assets/images/Hushdng.png')}}" alt="logo.png"></a>
                           
                        </div>
                        <div class="col-8 col-sm-9 col-md-9">
                            <nav class="navbar navbar-expand-md navbar-light  float-right">
                                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                    <ul class="navbar-nav ">
                                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a class="nav-link" href="{{url('/')}}">Home</a>
                                        </li>
                                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a class="nav-link" href="{{url('/about')}}">About us</a>
                                        </li>
                                        <li class="nav-item dropdown pl-4 pl-md-0 ml-0 ml-md-4 drop-menu">
                                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Skin Products
                                            </a>
                                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                                <li class="nav-item  pl-md-0 ml-0 md-4"><a class="dropdown-item " href="{{url('products/all')}}">All</a></li>
                                                @if(count($header_categories) > 0)
                                                    @foreach($header_categories as $head_row)
                                                        <li class="nav-item  pl-md-0 ml-0 md-4"><a class="dropdown-item " href="{{url('products/category/'.$head_row->slug)}}">{!!  commonHelper::textLimit($head_row->title , 30) !!}</a></li>
                                                    @endforeach
                                                @endif
                                            <!-- <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Subsubmenu action</a></li>
                                                <li><a class="dropdown-item" href="#">Another subsubmenu action</a></li>
                                              </ul>
                                            </li>
                                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Second subsubmenu</a>
                                              <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#">Subsubmenu action</a></li>
                                                <li><a class="dropdown-item" href="#">Another subsubmenu action</a></li>
                                              </ul>
                                            </li> -->
                                            </ul>
                                        </li>
                                       
                                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a class="nav-link" href="{{url('category/media')}}">Media</a>
                                        </li>
                                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a class="nav-link" href="{{url('services')}}">Services</a>
                                        </li>
                                        <li class="nav-item pl-4 pl-md-0 ml-0 ml-md-4">
                                            <a class="nav-link" href="{{url('contactus')}}">Get In Touch</a>
                                        </li>
                                    </ul>
                                </div>
                                
                            </nav>	
                        </div>
                    </div>
                          
                </div>
            </div>
             <!-- MOBILE-VIEW-HEADER-START -->
             <nav class="navbar mobile-header menu">
                <div class="container">
                    <div class="logo">
                        <a href="{{url('/')}}"><img class="img-fluid b-logo" src="{{asset('front/assets/images/Hushdng.png')}}" alt="logo.png"></a>
                    </div>
                    <ul class="navbar-nav  navbar-bg navlinks">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{url('/')}}">Home </a>
                        </li>
                       
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('/about')}}">About us</a>
                        </li>
                        <li class="nav-item dropdown  drop-menu">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Skin Products
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                                <li class="nav-item  pl-md-0 ml-0 md-4"><a class="dropdown-item " href="{{url('products/all')}}">All</a></li>
                                @if(count($header_categories) > 0)
                                    @foreach($header_categories as $head_row)
                                        <li class="nav-item  pl-md-0 ml-0 md-4"><a class="dropdown-item " href="{{url('products/category/'.$head_row->slug)}}">{!!  commonHelper::textLimit($head_row->title , 30) !!}</a></li>
                                    @endforeach
                                @endif
                            <!-- <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Subsubmenu action</a></li>
                                <li><a class="dropdown-item" href="#">Another subsubmenu action</a></li>
                              </ul>
                            </li>
                            <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle" href="#">Second subsubmenu</a>
                              <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Subsubmenu action</a></li>
                                <li><a class="dropdown-item" href="#">Another subsubmenu action</a></li>
                              </ul>
                            </li> -->
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('category/media')}}">Media</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('services')}}">Services</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('contactus')}}">Get In Touch</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('terms')}}">Terms & Conditions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{url('privacy/policy')}}">Privacy & Policy</a>
                        </li>

                    </ul>
                    <div class="navdropdown">
                        <div class="line1"></div>
                        <div class="line2"></div>
                        <div class="line3"></div>
                    </div>
                </div>
            </nav>
            <!-- MOBILE-VIEW-HEADER-END -->
    </header> 
<!----- HEADER-CSS-END ------>