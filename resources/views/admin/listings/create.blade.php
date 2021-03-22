@extends('admin.template')
@section('content')
<!-- Content Wrapper START -->
<div class="main-content">
    <?php if(Session::has('success_msg')) { ?>
        <div class="alert alert-block alert-success fade in" style="margin:10px 15px;">
            <button type="button" class="close close-sm" data-dismiss="alert">
                <i class="fa fa-times"></i>
            </button>
             <p style="text-align:center; margin-bottom:0px; ">  {{ Session::get('success_msg') }}  </p>
        </div>
    <?php } ?>
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
                                        
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" class="form-control" name="listing_title" id="listing_title" value="{{Request::old('listing_title')}}" placeholder="Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Listing Detail Title</label>
                                                    <input type="text" class="form-control" name="listing_detail_title" id="listing_detail_title" value="{{Request::old('listing_detail_title')}}" placeholder="Listing Detail Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Founded</label>
                                                     <input type="text" class="form-control" name="founded" id="founded" value="{{Request::old('founded')}}" placeholder="Founded" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Website Link</label>
                                                     <input type="text" class="form-control" name="websitelink" id="websitelink" value="{{Request::old('websitelink')}}" placeholder="Website Link" required>
                                                </div>
                                            </div>
                                        
                                        <div class="col-md-4">
                                               <div class="checkbox">
                                                    <input id="form-2-2" name="is_approved" type="checkbox" >
                                                    <label for="form-2-2">Is Follow?</label>
                                                </div>
                                            </div>
                                        </div>

                                             <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Phone Number</label>
                                                     <input type="text" class="form-control" name="phonenumber" id="phonenumber" value="{{Request::old('phonenumber')}}" placeholder="Phonenumber" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Email</label>
                                                     <input type="text" class="form-control" name="email" id="email" value="{{Request::old('email')}}" placeholder="Email" required>
                                                </div>
                                            </div>
                                        
                                        <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Fees</label>
                                                     <input type="text" class="form-control" name="fees" id="fees" value="{{Request::old('fees')}}" placeholder="Fees" required>
                                                </div>
                                            </div>
                                            </div>
                                            <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Address</label>
                                                    <input type="text" class="form-control" name="address" id="address" value="{{Request::old('address')}}" placeholder="Address" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Details</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="listing_detail" required>{{Request::old('listing_detail')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>test</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="tab_value_1" required>{{Request::old('tab_value_1')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>test2</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="tab_value_2" required>{{Request::old('tab_value_2')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>test3</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="tab_value_3" required>{{Request::old('tab_value_3')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
<!--                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox ">
                                                    <input id="form-2-1" name="is_recommended" type="checkbox" >
                                                    <label for="form-2-1">New Arrivals Product</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>-->
                                        {{-- <div class="row"> --}}
                                            {{-- <div class="col-md-6">
                                                <label>Category</label>
                                                <div class="mrg-top-0">
                                                    <select id="selectize-group" name="cat_id" required="">
                                                        <option value="">Select Category</option>
                                                        @if(count($categories) > 0)
                                                            @foreach($categories as $row)
                                                                <option value="{{$row->id}}">{{$row->title}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div> --}}
                                           
                                        {{-- </div> --}}
                                        
<!--                                        <div class="categories">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Sub Category</label>
                                                    <div class="mrg-top-10">
                                                        <select class="selectize-group" name="sub_cat_id" disabled="">
                                                            <option value="">Select Sub Category</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>-->
<!--                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="mrg-top-30">
                                                        <label style=" margin-right: 50px;">Stock</label>
                                                        <div class="radio radio-inline radio-primary">
                                                            <input type="radio" name="status" value="1" id="form-5-6" checked="">
                                                            <label for="form-5-6">In-Stock</label>
                                                        </div>
                                                        <div class="radio radio-inline radio-primary">
                                                            <input type="radio" name="status" value="0" id="form-5-7" class="is_pally" >
                                                            <label for="form-5-7">Out of Stock</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>-->
                                         <div class="row">
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark"> <b>Full Image</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        
                                                        <input class="d-none1" type="file" name="course_image" id="img-upload" >
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error"></p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Seo Related Html(printed in header)</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="headerhtml" required>{{Request::old('headerhtml')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark"> <b>Full Image</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        
                                                        <input class="d-none1" type="file" name="course_image" id="img-upload" >
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error"></p>
                                            </div>
                                        </div>
                                         <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Thumbnail slug</label>
                                                    <input type="text" class="form-control" name="course_title" id="course_title" value="{{Request::old('course_title')}}" placeholder="Title" required>
                                                </div>
                                            </div>
                                             
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark"> <b>Full Image</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        
                                                        <input class="d-none1" type="file" name="course_image" id="img-upload" >
                                                    </label>
                                                </div>
                                            </div>
                                        
                                        
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error"></p>
                                            </div>
                                        </div>
                                        
                                        
                                        
                                         <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Full image slug</label>
                                                    <input type="text" class="form-control" name="listing_fullimage_slug" id="listing_fullimage_slug" value="{{Request::old('listing_fullimage_slug')}}" placeholder="Full image slug" required>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Total Reviews</label>
                                                     <input type="text" class="form-control" name="listing_review_totalreviews" id="listing_review_totalreviews" value="{{Request::old('listing_review_totalreviews')}}" placeholder="Total Reviews" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Reviews Percent</label>
                                                     <input type="text" class="form-control" name="listing_review_reviewspercent" id="listing_review_reviewspercent" value="{{Request::old('listing_review_reviewspercent')}}" placeholder="Reviews Percent" required>
                                                </div>
                                            </div>
 
                                            </div>
                                            <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Select Country</label>
                                                    <select class="selectize-group" name="listing_country" id="listing_country" onchange="getCities(value)" required="" >
                                                        <option value="">Select Country</option>
                                                        @if(count($countries) > 0)
                                                            @foreach($countries as $row)
                                                                <option value="{{$row->id}}">{{$row->name}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                         <div class="cities">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Select City</label>
                                                    <div class="mrg-top-10">
                                                        <select class="selectize-group" name="listing_city" disabled="">
                                                            <option value="">Select City</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                          <div class="row">
                                            <div class="col-md-12">
                                                <label>Parent Category</label>
                                                <div class="mrg-top-0">
                                                    <select class="selectize-group" name="parent_cat_id" required="" onchange="getSubCategories(value)">
                                                        <option value="0">Select Parent Category</option>
                                                        @if(count($parent_categories) > 0)
                                                            @foreach($parent_categories as $row)
                                                                <option value="{{$row->cat_id}}">{{$row->cat_title}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="categories col-md-12" style="padding: 0px;">
                                                <div class="col-md-12">
                                                    <label>Child Category</label>
                                                    <div class="mrg-top-0">
                                                        <select class="selectize-group" name="cat_id" required="">
                                                            <option value="">Select Child Category</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Latitude</label>
                                                    <input type="text" class="form-control" name="listing_cordinate_latitude" id="listing_cordinate_latitude" value="{{Request::old('listing_cordinate_latitude')}}" placeholder="Latitude">
                                                </div>
                                            </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>longitude</label>
                                                    <input type="text" class="form-control" name="listing_cordinate_longitude" id="listing_cordinate_longitude" value="{{Request::old('listing_cordinate_longitude')}}" placeholder="longitude">
                                                </div>
                                            </div>
                                             
                                                
                                            </div>
                                            <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Slug</label>
                                                    <input type="text" class="form-control" name="listing_slug" id="listing_slug" value="{{Request::old('listing_slug')}}" placeholder="Slug">
                                                </div>
                                           
                                                
                                            
                                             <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox ">
                                                    <input id="form-2-2" name="is_approved" type="checkbox" >
                                                    <label for="form-2-2">Is Approved?</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button type="button" class="btn btn-default" onclick="clearFields();">Clear</button>
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
        $('#summernote-usage').val('');
        $('#product_title').val('');
        $('#product_price').val('');
        $('#bulk_price').val('');
        $('#product_discount').val('');
        $('#pally_size').val('');
    }
    $('#product_price').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('#bulk_price').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $(".is_pally").change(function(){ 
        if( $(this).is(":checked") ){ // check if the radio is checked
            var val = $(this).val(); // retrieve the value
            if(val == 1){
                $('.pally_size').show();
            }else{
                $('.pally_size').hide();
            }
        }
    });
    
    function getCities(id){
        $.ajax({
            type: 'GET',
            url: APP_URL+'/admin/cities/'+id,
            data: '',
            processData: false,
            contentType: false,
            success: function (d) {
                $('.cities').html(d);
            }
        });
    }
     function getSubCategories(id){
        $.ajax({
            type: 'GET',
            url: APP_URL+'/admin/sub/categories/'+id,
            data: '',
            processData: false,
            contentType: false,
            success: function (d) {
                $('.categories').html(d);
            }
        });
    }

</script>
@endsection
