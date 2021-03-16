@extends('admin.template')
@section('content')
<style>
    .selectize-control.multi .selectize-input [data-value] {
        color: #fff !important;
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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="mrg-top-30">
                                                        <div class="radio radio-inline radio-primary">
                                                            <input type="radio" name="is_user" value="user" id="form-5-1" class="is_user" checked="">
                                                            <label for="form-5-1">Select Users</label>
                                                        </div>
                                                        <div class="radio radio-inline radio-primary">
                                                            <input type="radio" name="is_user" value="All" id="form-5-2" class="is_user" >
                                                            <label for="form-5-2">All</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row select_user">
                                            <div class="col-md-12">
                                                <label>Users</label>
                                                <div class="form-group">
                                                    <div class="mrg-top-10">
                                                        <select id="selectize-tags-1" multiple class="item-info" name="user_id[]" required="">
                                                            <option value="" disabled selected>Select User</option>
                                                            @if(count($users) > 0)
                                                                @foreach($users as $row)
                                                                    <option value="{{$row->user_id}}" >{{$row->first_name.' '.$row->last_name}}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="mrg-top-10 mrg-btm-10">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Title</label>
                                                    <input type="text" class="form-control" name="title" id="product_title" value="{{Request::old('title')}}" placeholder="Title" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Message</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage1" name="message" required>{{Request::old('Message')}}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary">Send</button>
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
        $('#summernote-usage1').val('');
        $('#title').val('');
    }
    
    $(".is_user").change(function(){ 
        if( $(this).is(":checked") ){ // check if the radio is checked
            var val = $(this).val(); // retrieve the value
            if(val == 'All'){
                $('.select_user').hide();
            }else{
                $('.select_user').show();
            }
        }
    });
    
</script>
@endsection

        
             
        
      