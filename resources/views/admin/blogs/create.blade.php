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
                                                <p class="mrg-top-10 text-dark"> <b>Home Slider Image dimension: 2000x500</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload" class="pointer">
                                                        <img id="uploadPreview" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" width="118" alt="">
                                                        <span class="btn btn-default display-block no-mrg-btm" style="border-radius: 0px 0px 5px 5px;">Choose file</span>
                                                        <input class="d-none" type="file" name="image" id="img-upload" onchange="PreviewImage();">
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
                                                    <input type="text" class="form-control" name="title" id="title" value="{{Request::old('title')}}" placeholder="Title">
                                                </div>
                                            </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Seo Title</label>
                                                    <input type="text" class="form-control" name="seo_title" id="seo_title" value="{{Request::old('seo_title')}}" placeholder="Seo Title">
                                                </div>
                                            </div>
                                            
                                        </div>
                                         <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <input type="text" class="form-control" name="description" id="description" value="{{Request::old('description')}}" placeholder="description">
                                                </div>
                                            </div>
                                              <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Image Alt Tag</label>
                                                    <input type="text" class="form-control" name="image_slug" id="image_slug" value="{{Request::old('image_slug')}}" placeholder="image alt tag">
                                                </div>
                                            </div>
                                                
                                            </div>
                                             <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Mata Keyword</label>
                                                    <input type="text" class="form-control" name="meta_keyword" id="meta_keyword" value="{{Request::old('meta_keyword')}}" placeholder="Keyword">
                                                </div>
                                            </div>
                                             
                                                
                                            </div>
                                            
                                            <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Content</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="content" required>{{Request::old('content')}}</textarea>
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
                                                    <label>Slug</label>
                                                    <input type="text" class="form-control" name="slug" id="slug" value="{{Request::old('slug')}}" placeholder="Slug">
                                                </div>
                                                
                                            </div>
                                             <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Tags</label>
                                                    <input type="text" class="form-control" name="tags" id="tags" value="{{Request::old('tags')}}" placeholder="tags">
                                                </div>
                                            </div>
                                            
                                        </div>
                                         
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox ">
                                                    <input id="form-2-1" name="is_approved" type="checkbox" >
                                                    <label for="form-2-1">is Approved</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="checkbox ">
                                                    <input id="form-2-2" name="isIndex" type="checkbox" >
                                                    <label for="form-2-2">Is Index</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
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