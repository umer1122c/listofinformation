<!-- Side Nav START -->
            <div class="side-nav">
                <div class="side-nav-inner">
                    <div class="side-nav-logo">
                        <a href="{{URL::to('admin/dashboard')}}" style="max-width: 250px;padding: 6px 0 7px;">
                            @if(file_exists( public_path().'/settings/'.$getSettings->site_logo ))
                                <img class="img-responsive inline-block" src=" {{url('').'/settings/'.$getSettings->site_logo}}" style="width: 150px;margin: 10px;" alt="">
                            @else
                                <img class="img-responsive inline-block" src="{{asset('admins/assets/images/logo/logo.png')}}" style="width: 150px;margin: 10px;" alt="">
                            @endif
<!--                            <div class="logo logo-dark" style="background-image: url('../admin/assets/images/logo/logo.png')"></div>
                            <div class="logo logo-white" style="background-image: url('../admin/assets/images/logo/logo-white.png')"></div>-->
                        </a>
                        <div class="mobile-toggle side-nav-toggle">
                            <a href="">
                                <i class="ti-arrow-circle-left"></i>
                            </a>
                        </div>
                    </div>
                    <ul class="side-nav-menu scrollable">
                        <li class="nav-item active">
                            <a class="" href="{{URL::to('admin/dashboard')}}">
                                <span class="icon-holder">
                                    <i class="ti-home"></i>
                                </span>
                                <span class="title">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown <?php if($class == 'courses'){ echo 'open'; } ?>">
                            <a class="dropdown-toggle" href="javascript:void(0);">
                                <span class="icon-holder">
                                    <i class=" ei-book"></i>
                                </span>
                                <span class="title">List Of Information</span>
                                <span class="arrow">
                                    <i class="ti-angle-right"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{URL::to('admin/listings/1')}}">Education</a>
                                </li>
                                 <li>
                                    <a href="{{URL::to('admin/listings/2')}}">Hotel</a>
                                </li>
                                 <li>
                                    <a href="{{URL::to('admin/listings/3')}}">Medical</a>
                                </li>
                            </ul>
                        </li>
                         <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/blogs')}}">
                                <span class="icon-holder">
                                    <i class="ei-file-image"></i>
                                </span>
                                <span class="title">Blogs</span>
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/countries')}}">
                                <span class="icon-holder">
                                    <i class="ei-file-image"></i>
                                </span>
                                <span class="title">Manage Countries</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/cities')}}">
                                <span class="icon-holder">
                                    <i class=" ti-blackboard"></i>
                                </span>
                                <span class="title">Manage Cities</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/categories/0')}}">
                                <span class="icon-holder">
                                    <i class="ei-archive"></i>
                                </span>
                                <span class="title">Manage Categories</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/advertisement')}}">
                                <span class="icon-holder">
                                    <i class="ei-file-image"></i>
                                </span>
                                <span class="title">Manage Advertisement</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/users')}}">
                                <span class="icon-holder">
                                    <i class="ei-users"></i>
                                </span>
                                <span class="title">Manage Users</span>
                            </a>
                        </li>
                        <li class="nav-item dropdown <?php if($class == 'orders'){ echo 'open'; } ?>">
                            <a class="dropdown-toggle" href="javascript:void(0);">
                                <span class="icon-holder">
                                    <i class="ei-money"></i>
                                </span>
                                <span class="title">Manage Orders</span>
                                <span class="arrow">
                                    <i class="ti-angle-right"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{URL::to('admin/orders')}}">Manage Orders</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown <?php if($class == 'courses'){ echo 'open'; } ?>">
                            <a class="dropdown-toggle" href="javascript:void(0);">
                                <span class="icon-holder">
                                    <i class=" ei-book"></i>
                                </span>
                                <span class="title">Manage Courses</span>
                                <span class="arrow">
                                    <i class="ti-angle-right"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{URL::to('admin/courses')}}">Manage Courses</a>
                                </li>
                            </ul>
                        </li>

                        
                        <li class="nav-item active">
                            <a class="" href="{{URL::to('admin/newslatter/list')}}">
                                <span class="icon-holder">
                                    <i class="ei-speech-box-text"></i>
                                </span>
                                <span class="title">Manage Newsletter</span>
                            </a>
                        </li>
<!--                        <li class="nav-item dropdown <?php if($class == 'settings'){ echo 'open'; } ?>">
                            <a class="dropdown-toggle" href="javascript:void(0);">
                                <span class="icon-holder">
                                    <i class="ei-tools"></i>
                                </span>
                                <span class="title">Manage Settings</span>
                                <span class="arrow">
                                    <i class="ti-angle-right"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{URL::to('admin/settings')}}">Settings</a>
                                </li>
                                
                            </ul>
                        </li>-->
                        <li class="nav-item dropdown <?php if($class == 'settings'){ echo 'open'; } ?>">
                            <a class="dropdown-toggle" href="javascript:void(0);">
                                <span class="icon-holder">
                                    <i class="ei-tools"></i>
                                </span>
                                <span class="title">Manage Settings</span>
                                <span class="arrow">
                                    <i class="ti-angle-right"></i>
                                </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{URL::to('admin/settings')}}">Settings</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="dropdown-toggle " href="{{URL::to('admin/logout')}}">
                                <span class="icon-holder">
                                    <i class="ei-log-out"></i>
                                </span>
                                <span class="title">Logout</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Side Nav END -->