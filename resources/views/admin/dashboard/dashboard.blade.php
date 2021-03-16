@extends('admin.template')
@section('content')
<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            
            <div class="col-lg-3">
                <div class="card">
                    <div class="card-block">
                        <div class="inline-block">
                            <h1 class="no-mrg-vertical">{{$users}}</h1>
                            <p>Total Users</p>
                        </div>
                        <div class="mrg-top-25">
                            <div id="bar-config"></div>
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="col-lg-3">
                <div class="card">
                    <div class="card-block">
                        <div class="inline-block">
                            <h1 class="no-mrg-vertical">₦</h1>
                            <p>Total Sales</p>
                        </div>
                        <div class="mrg-top-25">
                            <div id="bar-config"></div>
                        </div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
</div>
<!-- Content Wrapper END -->
@endsection