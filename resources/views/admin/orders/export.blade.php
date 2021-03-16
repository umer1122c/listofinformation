@extends('stores.template')
@section('content')
<style>
    .dt-button.buttons-excel.buttons-html5 {
        padding: 7px 20px;
        background: #334697;
        border: 1px solid #334697;
        color: #fff;
        border-radius: 3px;
        margin-bottom: 15px;
        
    }
    .dt-button.buttons-csv.buttons-html5 {
        padding: 7px 20px;
        background: #334697;
        border: 1px solid #334697;
        color: #fff;
        border-radius: 3px;
        margin-bottom: 15px;
    }
</style>
<div class="row">
	<? if(Session::has('success_msg')) { ?>
        <div class="alert alert-block alert-success fade in" style="margin:10px 15px;">
        <button type="button" class="close close-sm" data-dismiss="alert">
            <i class="fa fa-times"></i>
        </button>
        
         <p style="text-align:center;">  {{ Session::get('success_msg') }}  </p>
        
        </div>
    <? } ?>
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                <?php echo $table; ?>
                <span class="tools pull-right">
                    <a href="javascript:;" class="fa fa-chevron-down"></a>
                    <a href="javascript:;" class="fa fa-cog"></a>
                    <a href="javascript:;" class="fa fa-times"></a>
                 </span>
            </header>
            <div class="panel-body">
            <div class="adv-table">
            <div class="clearfix">
                <div class="btn-group">
                    
                </div>
                <!--<div class="btn-group pull-right">
                    <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">Tools <i class="fa fa-angle-down"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="#">Print</a></li>
                        <li><a href="#">Save as PDF</a></li>
                        <li><a href="#">Export to Excel</a></li>
                    </ul>
                </div>-->
            </div>
            <div class="space15"></div>
            <table  class="display table table-bordered table-striped" id="example">
            <thead>
            <tr>
                <th> TransactionID</th>
                <th> User Name</th>
                <th>Total</th>
                <th>Order Status</th>
                <th>Delivery Time</th>
                <th>Address</th>
                <th>Products</th>
            </tr>
            </thead>
            <tbody>
            @if($transactions)
                @foreach($transactions as $row)
                    <tr>
                        <td>{{$row->TransactionID}}</td>
                        <td>{{$row->name}}</td>
                        <td>{{$row->Total}}</td>
                        <td>{{$row->PaymentStatus}}</td>
                        <td>{{$row->dilivery_time}}</td>
                        <td>{{$row->house_name.' '.$row->street.' '.$row->town.' '.$row->county.' '.$row->postcode }}</td>
                        <td>
                            <?php $products = commonHelper::getProducts($row->TransactionID);  
                                $productArray = [];
                                    if(count($products) > 0){
                                        foreach($products as $prod_row){
                                            $productArray[] = $prod_row->product_name;
                                        }
                                        echo implode(" | ",$productArray);
                                    }
                            ?>
                        </td>
                        
                    </tr>
                @endforeach
            @endif
            </table>
            </div>
            </div>
        </section>
    </div>
</div>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="confirmDelete" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Confirm Request</h4>
            </div>
            <div class="modal-body">
    
                Are you sure you want to accept/reject this order?
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" onclick="delete_confirm()"> Confirm</button>
            </div>
        </div>
    </div>
</div> 
<script type="text/javascript"> 
    var delID = 0;
    var statusID = 0;
    function delete_confirm(){
        if(delID != 0){
            window.location = APP_URL+'/store/change/order/status/'+statusID+'/'+ delID;
        }
    } 
 
    function delete_record(del_id , status){
        delID = del_id;
        statusID = status;
        $("#confirmDelete").modal("show");
    }      
</script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
@endsection 
 