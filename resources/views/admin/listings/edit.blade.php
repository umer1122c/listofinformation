@extends('admin.template')
@section('content')
<style>
    .col-md-2 {
        float: left;
    }
</style>
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
                                                        @if($course->course_image != '')
                                                            <img id="uploadPreview" src="{{asset('courses/'.$course->course_image)}}" width="118" alt="">
                                                        @else
                                                            <img id="uploadPreview" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" width="118" alt="">
                                                        @endif
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
                                                    <input type="text" class="form-control" name="course_title" id="course_title" value="{{$course->course_title}}" placeholder="Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Meta Title</label>
                                                    <input type="text" class="form-control" name="mata_title" id="mata_title" value="{{$course->mata_title}}" placeholder="Meta Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Meta Description</label>
                                                    <textarea class="form-control" rows="3" name="mata_description">{{$course->mata_description}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Category</label>
                                                <div class="mrg-top-0">
                                                    <select id="selectize-group" name="cat_id" required="">
                                                        <option value="">Select Category</option>
                                                        @if(count($categories) > 0)
                                                            @foreach($categories as $row)
                                                                <option value="{{$row->id}}" <?php if($course->cat_id == $row->id){ ?> selected="" <?php } ?>>{{$row->title}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Price</label>
                                                    <input type="number" class="form-control" name="price" id="product_price" value="{{$course->price}}" placeholder="0" required>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Description</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="course_description" required>{{$course->html_description}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Submit</button>
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
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="confirmDelete" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
    
                Are you sure you want to delete this Product Image?
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" onclick="delete_confirm()"> Confirm</button>
            </div>
        </div>
    </div>
</div>
<!-- Content Wrapper END -->
<script>
    function clearFields(){
        $('#form-1-5').val('');
        $('#product_title').val('');
        $('#product_price').val('');
        $('#product_discount').val('');
    }
    var delID = 0;
    function delete_confirm(){
        if(delID != 0){
            var product_id = '<?php echo $product_id; ?>';
            window.location = APP_URL+'/admin/product/deleteimage/'+product_id +'/'+ delID;
        }
    } 
    function delete_record(del_id){
        delID = del_id;
        $("#confirmDelete").modal("show");
    }
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
        $('select[name="sub_cat_id"]').attr('disabled', 'disabled');
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