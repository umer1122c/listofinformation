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
                                                <p class="mrg-top-10 text-dark"> <b>Home Category Image dimension: 555x301</b></p>
                                            </div>
                                            <div class="col-md-6">
                                                <div>
                                                    <label for="img-upload1" class="pointer">
                                                        @if($category->cat_image != '')
                                                            <img id="uploadPreview1" src="{{asset('categories/'.$category->cat_image)}}" width="118" alt="">
                                                        @else
                                                            <img id="uploadPreview1" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" width="118" alt="">
                                                        @endif
                                                        <span class="btn btn-default display-block no-mrg-btm" style="border-radius: 0px 0px 5px 5px;">Choose file</span>
                                                        <input class="d-none" type="file" name="image" id="img-upload1" onchange="PreviewImage1();">
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <p class="mrg-top-10 text-dark" style=" color: #ff3c7e;" id="upload_logo_error1"></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Category Type</label>
                                                    <select class="selectize-group" name="cat_type" id="cat_type" required="" >
                                                        <option value="listing" @if($category->cat_type == 'listing') selected @endif>Listing</option>
                                                        <option value="blog" @if($category->cat_type == 'blog') selected @endif>Blog</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" class="form-control" name="cat_title" id="cat_title" value="{{$category->cat_title}}" placeholder="Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Meta Title</label>
                                                    <input type="text" class="form-control" name="title" id="title" value="{{$category->title}}" placeholder="Meta Title">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" rows="3" id="form-1-5" name="description">{{$category->description}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Update</button>
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
        $('#form-1-5').val('');
        $('#title').val('');
        
    }
</script>
@endsection