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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Select Post</label>
                                                    <select class="selectize-group" name="postid" id="postid" required="" >
                                                        <option value="0">Select Post</option>
                                                        @if(count($posts) > 0)
                                                            @foreach($posts as $row)
                                                                <option value="{{$row->postid}}" <?php if($adds->postid == $row->postid) { echo "selected"; }  ?>>{{$row->title}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Select Position</label>
                                                    <select class="selectize-group" name="position" id="position" required="" >
                                                        <option value="" >Select Position</option>
                                                        <option value="home_top" <?php if($adds->position == 'home_top') { echo "selected"; }  ?>>Home Top</option>
                                                        <option value="home_sidebar_top" <?php if($adds->position == 'home_sidebar_top') { echo "selected"; }  ?>>Home Sidebar Top</option>
                                                        <option value="home_sidebar_bottom" <?php if($adds->position == 'home_sidebar_bottom') { echo "selected"; }  ?>>Home Sidebar Buttom</option>
                                                        <option value="blog_sidebar_top" <?php if($adds->position == 'blog_sidebar_top') { echo "selected"; }  ?>>Blog Sidebar Top</option>
                                                        <option value="blog_sidebar_bottom" <?php if($adds->position == 'blog_sidebar_bottom') { echo "selected"; }  ?>>Blog Sidebar Buttom</option>
                                                        <option value="blog_listing" <?php if($adds->position == 'blog_listing') { echo "selected"; }  ?>>Blog Listing</option>
                                                        <option value="footer" <?php if($adds->position == 'footer') { echo "selected"; }  ?>>Footer</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label>Add Advertisement Content</label>
                                                    <textarea class="form-control" rows="3" id="summernote-usage" name="add_content" placeholder="Add Advertisement Content" required>{{$adds->add_content}}</textarea>
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
        $('#form-1-5').val('');
        $('#title').val('');
        
    }
</script>
@endsection