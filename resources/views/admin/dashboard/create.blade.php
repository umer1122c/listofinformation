@extends('admin.template')
@section('content')
<!-- Content Wrapper START -->
<div class="main-content">
    <div class="container-fluid">
        
        <div class="row">
            @include('common.errors')
            <div class="col-md-12">
                <div class="card">
                    
                    <div class="card-heading border bottom">
                        <h4 class="card-title">{{$table}}</h4>
                    </div>
                    <div class="card-block">
                        <div class="card-block">
                            <div class="row">
                                <div class="col-md-8 ml-auto mr-auto">
                                    <form role="form" id="form-validation" action="" method="post" enctype="multipart/form-data">
                                        {!! csrf_field() !!}
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark"> <b>Image</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        @if($admins->avater != '')
                                                            <img id="uploadPreview" src="{{asset('users/'.$admins->avater)}}" width="118" alt="">
                                                        @else
                                                            <img id="uploadPreview" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" width="118" alt="">
                                                        @endif
                                                        <span class="btn btn-default display-block no-mrg-btm" style="border-radius: 0px 0px 5px 5px;">Choose file</span>
                                                        <input class="d-none" type="file" name="avater" id="img-upload" onchange="PreviewImage();">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error"></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" class="form-control" name="admin_name" id="admin_name" value="{{$admins->admin_name}}" placeholder="User Name" required>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" class="form-control" name="admin_email" id="admin_email" value="{{$admins->admin_email}}" placeholder="Email" required readonly="">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Password</label>
                                                    <input type="password" class="form-control" name="admin_password" id="password" value="" placeholder="Password">
                                                </div>
                                            </div>
                                            
                                        </div>
<!--                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Confirm Password</label>
                                                    <input type="password" class="form-control" name="confirm_password" id="user_name" value="" placeholder="Confirm Password">
                                                </div>
                                            </div>
                                            
                                        </div>-->
                                        
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <!--<button type="button" class="btn btn-default" onclick="clearFields();">Clear</button>-->
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->
<script>
    function clearFields(){
        $('#form-1-5').val('');
        $('#title').val('');
        
    }
</script>
@endsection