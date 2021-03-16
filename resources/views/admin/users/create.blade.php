 @extends('admin.template')
@section('content')
<style>
.radio, .checkbox{
	padding-left:0px;
}
.icheck div, .icheck .disabled {
    float: left;
}
.icheck .single-row {
    display: inline-block;
    width: 100%;
}
</style>
<div class="row">
	<div class="col-lg-12">
     @include('common.errors')
         <section class="panel">
                <header class="panel-heading">
                    <?php echo $table; ?>
                </header>
                <div class="panel-body">
                    <div class="position-center">
                        <form role="form" method="post" action="" enctype="multipart/form-data">
                        {!! csrf_field() !!}
                        <div class="form-group">
                            <label class="col-md-12" style="padding:0px;">User Image</label>
                            <div class="col-md-12" style="padding:0px;">
                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                    <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                            <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" />
                                    </div>
                                    <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                    <div>
                                       <span class="btn btn-white btn-file">
                                       <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                       <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                       <input type="file" name="user_image" class="default" />
                                       </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">First Name</label>
                            <input type="text" name="first_name" value="{{Request::old('first_name')}}" placeholder="First Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Last Name</label>
                            <input type="text" name="last_name" value="{{Request::old('last_name')}}" placeholder="Last Name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email</label>
                            <input type="text" name="email" value="{{Request::old('email')}}" placeholder="Email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Numbers Of Properties</label>
                            <input type="text" name="no_of_properties" value="{{Request::old('no_of_properties')}}" placeholder="Numbers Of Properties" class="form-control">
                        </div>
                         <div class="form-group">
                            <label for="exampleInputEmail1">Phone</label>
                            <input type="text" name="phone" value="{{Request::old('phone')}}" placeholder="Phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Contact Info</label>
                            <input type="text" name="contact_info" value="{{Request::old('contact_info')}}" placeholder="Contact Info" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Business Phone</label>
                            <input type="text" name="business_phone" value="{{Request::old('business_phone')}}" placeholder="Business Phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Password</label>
                            <input type="password" name="password" value="{{Request::old('password')}}" placeholder="Password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Confirm Password</label>
                            <input type="password" name="confirm_password" value="{{Request::old('confirm_password')}}" placeholder="Confirm Password" class="form-control">
                        </div>
                        
                        <input type="submit" class="btn btn-info" value="Add User" />
                    </form>
                    </div>

                </div>
            </section> 
    </div>
</div> 
@endsection        
        
             
        
      