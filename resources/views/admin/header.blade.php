<!-- Header START -->
<style>
.header .header-container .nav-right .dropdown-menu > li > a {
    padding: 10px 10px !important;
}
</style>
<div class="header navbar">
    <div class="header-container">
        <ul class="nav-left">
            <li>
                <a class="side-nav-toggle" href="javascript:void(0);">
                    <i class="ti-view-grid"></i>
                </a>
            </li>
<!--            <li class="search-box">
                <a class="search-toggle no-pdd-right" href="javascript:void(0);">
                    <i class="search-icon ti-search pdd-right-10"></i>
                    <i class="search-icon-close ti-close pdd-right-10"></i>
                </a>
            </li>-->
        </ul>
        <ul class="nav-right">
            <li class="user-profile dropdown">
                <a href="" class="dropdown-toggle" data-toggle="dropdown">
                    @if(Session::get('avater') == '')
                        <img class="profile-img img-fluid" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" style="width: 35px;height: 35px;object-fit: scale-down;" alt="">
                    @else
                        <img class="profile-img img-fluid" src="{{asset('users/'.Session::get('avater'))}}" style="width: 35px;height: 35px;object-fit: scale-down;" alt="">
                    @endif
                    <div class="user-info">
                        <span class="name pdd-right-5">{{Session::get('admin_name')}}</span>
                        <i class="ti-angle-down font-size-10"></i>
                    </div>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{URL::to('profile')}}">
                            <i class="ti-user pdd-right-5"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{URL::to('admin/change/password')}}">
                            <i class="ti-lock pdd-right-5"></i>
                            <span>Change Password</span>
                        </a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li>
                        <a href="{{URL::to('admin/logout')}}">
                            <i class="ti-power-off pdd-right-5"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
<!-- Header END -->