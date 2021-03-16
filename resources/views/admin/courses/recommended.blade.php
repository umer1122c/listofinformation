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
                                
                            </div>
                            <div class="table-overflow">
                                <table id="dt-opt" class="table table-lg table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Bulk Price</th>
                                            <th>Retail Price</th>
                                            <th>Size</th>
<!--                                            <th>Product Season</th>-->
<!--                                            <th>Out of stock</th>-->
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($products) > 0)
                                            @foreach($products as $row)
                                                <tr>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->product_title}}</span>
                                                        </div>
                                                    </td>
                                                    <td width="28%">
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->product_description}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="pdd-left-20">{{$row->product_price}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="pdd-left-20">{{$row->bulk_price}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="pdd-left-20">{{$row->pally_size}}</span>
                                                        </div>
                                                    </td>
<!--                                                    <td>
                                                        @if($row->is_season == '1')
                                                            <a href="{{URL::to('admin/product/season/0/'.$row->id)}}" class="btn btn-success"><span>In-Season</span></a>
                                                        @else
                                                            <a href="{{URL::to('admin/product/season/1/'.$row->id)}}" class="btn btn-danger"><span>Out of Season</span></a>
                                                        @endif
                                                    </td>-->
<!--                                                    <td>
                                                        @if($row->status == '1')
                                                            <a href="{{URL::to('admin/product/stock/0/'.$row->id)}}" class="btn btn-success"><span>In-Stock</span></a>
                                                        @else
                                                            <a href="{{URL::to('admin/product/stock/1/'.$row->id)}}" class="btn btn-danger"><span>Out of Stock</span></a>
                                                        @endif
                                                    </td>-->
                                                    <td>
                                                        
                                                        <a href="javascript:void(0);" class="btn btn-danger" onclick="delete_record({{$row->id}})">
                                                            <i class="ti-trash pdd-right-5"></i>
                                                            <span>Remove</span>
                                                        </a>
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
                <h4 class="modal-title"></h4>
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                
            </div>
            <div class="modal-body">
    
                Are you sure you want to remove this form new arrivals product?
                
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
        if(delID != 0){
            window.location = APP_URL+'/admin/product/recommended/remove/'  + delID;
        }
    } 

    function delete_record(del_id){
        delID = del_id;
        $("#confirmDelete").modal("show");
    }      
</script>
@endsection 
 