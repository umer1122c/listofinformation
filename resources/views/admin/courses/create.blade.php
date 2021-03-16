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
                                        <div class="row">
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark"> <b>Course Image dimension: 500x500</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        <img id="uploadPreview" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" width="118" alt="">
                                                        <span class="btn btn-default display-block no-mrg-btm" style="border-radius: 0px 0px 5px 5px;">Choose file</span>
                                                        <input class="d-none" type="file" name="course_image" id="img-upload" onchange="PreviewImage();">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error"></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" class="form-control" name="course_title" id="course_title" value="{{Request::old('course_title')}}" placeholder="Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Meta Title</label>
                                                    <input type="text" class="form-control" name="mata_title" id="mata_title" value="{{Request::old('mata_title')}}" placeholder="Meta Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Meta Description</label>
                                                    <textarea class="form-control" rows="3" name="mata_description">{{Request::old('mata_description')}}</textarea>
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
                                        <div class="row">
                                            <div class="col-md-6">
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
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label> Price</label>
                                                    <input type="number" class="form-control" name="price" id="price" value="{{Request::old('price')}}" placeholder="0" required>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
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
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="course_description" required>{{Request::old('course_description')}}</textarea>
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
