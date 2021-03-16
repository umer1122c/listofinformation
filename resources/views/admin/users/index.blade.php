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
            <div class="page-title">
                <h4><?php echo $table; ?></h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-block">
                            <div class="clearfix">
<!--                                <div class="btn-group">
                                    <a href="{{URL::to('admin/category/add')}}"><button id="editable-sample_new" class="btn btn-primary">Add New <i class="fa fa-plus"></i></button></a>
                                </div>-->
                            </div>
                            <div class="table-overflow">
                                <table id="dt-opt" class="table table-lg table-hover">
                                    <thead>
                                        <tr>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Email</th>
                                            <th>Image</th>
                                            <th>Actions</th>
                                            <!--<th></th>
                                            <th>Status</th>
                                            <th>Bill Code</th>
                                            <th>Date</th>
                                            <th>Amount</th>-->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($users)
                                            @foreach($users as $row)
                                                <tr>
                                                    <td width="20%">
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->first_name}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->last_name}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->email}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="list-info mrg-top-10">
                                                            @if($row->user_image != '')
                                                                <img class="thumb-img" src="{{asset('users/'.$row->user_image)}}" alt="">
                                                            @else
                                                                <img class="thumb-img" src="https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="">
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td width='35%'>
                                                        
                                                        <a href="{{URL::to('admin/pally/links/'.$row->user_id)}}" class="btn btn-success">
                                                            <i class="ti-link pdd-right-5"></i>
                                                            <span>Pally Links</span>
                                                        </a>
                                                        <a href="{{URL::to('admin/user/edit/'.$row->user_id)}}" class="btn btn-info">
                                                            <i class="ti-export pdd-right-5"></i>
                                                            <span>Edit</span>
                                                        </a>
                                                        @if($row->status == '1')
                                                            <a href="{{URL::to('admin/users/0/'.$row->user_id)}}" class="btn btn-success"><i class="ei-circle pdd-right-5"></i><span>Access</span></a>
                                                        @else
                                                            <a href="{{URL::to('admin/users/1/'.$row->user_id)}}" class="btn btn-danger"><i class="ti-trash pdd-right-5"></i><span>Block</span></a>
                                                        @endif
                                                    </td>
                                                    
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Wrapper END -->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="confirmDelete" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm Delete</h4>
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                
            </div>
            <div class="modal-body">
    
                Are you sure you want to delete this Category?
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="delete_confirm()"> Confirm</button>
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript"> 
    var delID = 0;
    function delete_confirm(){
        alert();
        if(delID != 0){
            window.location = APP_URL+'/admin/category/delete/'  + delID;
        }
    } 

    function delete_record(del_id){
        delID = del_id;
        $("#confirmDelete").modal("show");
    }      
</script>
@endsection 
 