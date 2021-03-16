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
                                            
                                            
                                            <th style=" display:none;">Date Created</th>
                                            <th> TransactionID</th>
                                            <th> User Name</th>
                                            <th> Order Type</th>
                                            <th>Total</th>
                                            <th width='15%'>Payment Status</th>
                                            <th>Created at</th>
                                            <th>Due delivery date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($orders)
                                            @foreach($orders as $row)
                                                <tr>
                                                    
                                                    
                                                    <td style=" display:none;">
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->id}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->order_id.'-'.$row->id}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->first_name.' '.$row->last_name}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">
                                                                @if($row->order_type == 1)
                                                                    Normal Order
                                                                @else
                                                                    Pally Order
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">₦{{number_format($row->order_total + $row->shipping_cost - $row->discount_amount,2)}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->status}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->created_at}}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="relative mrg-top-15">
                                                            <span class="">{{$row->dilivery_date}}</span>
                                                        </div>
                                                    </td>
                                                    <td width='10%'>
                                                        <a href="{{URL::to('admin/order/update/status/'.$row->order_id)}}" class="btn btn-warning">
                                                            <i class="ti-eye pdd-right-5"></i>
                                                            <span>Update Status</span>
                                                        </a>
                                                        <a href="{{URL::to('admin/order/detail/'.$row->order_id)}}" class="btn btn-info">
                                                            <i class="ti-eye pdd-right-5"></i>
                                                            <span>Order Detail</span>
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
 