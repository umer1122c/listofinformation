@extends('admin.template')
@section('content')
<style>
    .modal-content{
        width: 100%;
        height: 100%;
        top: 0;
        position: relative;

    }
    .list-info img.thumb-img {
        height: 50px;
        width: 50px;
    }
</style>
<input type="hidden" id="_token" value="{{csrf_token()}}">
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
                <h4></h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-block">
                            <div class="clearfix">
<!--                                <div class="btn-group">
                                    <a href="{{URL::to('admin/product/add')}}" target="_blank"><button id="editable-sample_new" class="btn btn-primary">Add New <i class="fa fa-plus"></i></button></a>
                                </div>-->
                            </div>
                            <div class="table-overflow">
                                <table  id="dataTable" class="table table-lg table-hover" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Order ID</th>
                                        <th>Device Type</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Total</th>
                                        <th>Payment Status</th>
                                        <th>Created at</th>
                                        <th>Due delivery date</th>
                                        <th>Orders no of time</th>
                                        <th>Registered On</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="custom_message" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Update Order Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body alert_message">
                    <form class="form-horizontal mrg-top-40 pdd-right-30" id="artical_form" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label for="form-1-1" class="col-md-3 control-label">Delivery Date</label>
                            <div class="col-md-9">
                                <input type="hidden" value="0" name="id" class="form-control" id="record_id" placeholder="id">
                                <input type="text" class="form-control datepicker-1" name="dilivery_date" id="dilivery_date" value="" placeholder="Title" readonly="" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="form-1-1" class="col-md-3 control-label">Status</label>
                            <div class="col-md-9">
                                <select class="selectize-group" name="status" id="status" required="" >
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Delivered">Delivered</option>    
                                    <option value="Canceled">Canceled</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save_btn btn_save" >Save</button>
                    <button type="button" class="btn btn-danger btn_cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    <div id="refund_message" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Refund Amount</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body alert_message">
                    <form class="form-horizontal mrg-top-40 pdd-right-30" id="artical_form" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label for="form-1-1" class="col-md-3 control-label">Amount</label>
                            <div class="col-md-9">
                                <input type="hidden" value="0" name="order_id" class="form-control" id="order_id" placeholder="id">
                                <input type="hidden" value="0" name="user_id" class="form-control" id="user_id" placeholder="user_id">
                                <input type="number" class="form-control" name="amount" min="0" id="amount" value="" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="form-1-1" class="col-md-3 control-label">Note</label>
                            <div class="col-md-9">
                                <textarea class="form-control" name="note" rows="3" id="note" value="" placeholder="Note"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success btn_save_refund" >Save</button>
                    <button type="button" class="btn btn-danger btn_cancel">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="delete_modal" class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body alert_message">
                    Are you sure to delete this record ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success save_btn delete_yes" data-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-danger btn_cancel">No</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script type="text/javascript">

        $(document).ready(function(e){
            id =0;
            base_url = "{{url('/')}}";

            delete_id = 0;

            var table = $('#dataTable').DataTable( {
                dom: 'Blfrtip',

                "processing": true,
                "pageLength": 10,
                "serverSide": true,
                "ajax": {
                    "url": '<?php echo url("/orders-list") ?>',
                    "type": "GET"
                },
                "columns": [

                    {data: "id", name: "id", "visible": false},
                    {data: "order_id", name: "order_id"},
                    {data: "deviceType", name: "deviceType"},
                    {data: "first_name", name: "users.first_name"},
                    {data: "last_name", name: "users.last_name"},
                    {data: "total", name: "total", orderable: false, searchable: false},
                    {data: "status", name: "status"},
                    {data: "created_at", name: "created_at"},
                    { "data": 'dilivery_date' , 
                        "render" : function ( data, type, full ) { 
                           return full['dilivery_date'];},
                       "title": "Due Dilivery Date",
                       orderable: false, searchable: false
                    },
                    {data: "no_of_orders", name: "no_of_orders", orderable: false, searchable: false},
                    {data: "register_on", name: "register_on", orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
            } );

            $(".dataTables_filter input")
                .unbind()
                .bind("input", function(e) {
                    if(this.value.length >= 2 || e.keyCode == 13) {
                        table.search(this.value).draw();
                    }
                    if(this.value == "") {
                        table.search("").draw();
                    }
                    return;
                });

            $("body").on("click",".edit_button",function(e){
                var dilivery_date = $(this).attr("dilivery_date");
                var status = $(this).attr("status");
                var id = $(this).attr("id");
                $("#record_id").val(id);
                $("#dilivery_date").val(dilivery_date);
                var selectElement = $('#status').eq(0);
                var selectize = selectElement.data('selectize');
                if (!!selectize) selectize.setValue(status);
                 $("#custom_message").modal("show");
            });
            
            $("body").on("click",".btn_save",function(e){
                var _token = $('input#_token').val();
                var status = $('#status').val();
                var dilivery_date = $("#dilivery_date").val();
                var record_id = $("#record_id").val();
                $.ajax({
                    method:"POST",
                    url:'<?php echo url("update-status") ?>',
                    data:{id:record_id,status:status,dilivery_date:dilivery_date,_token:_token},
                    success:function(res){
                        if(res.status == true){
                            $.notify(res.message, 'success');
                            $("#custom_message").modal("hide");
                            table.ajax.reload( null, false );
                        }else{
                            $.notify(res.message, 'error');
                        }
                    },
                    error: function(err) {
                        $.notify('Error occurred while saving.', 'error');
                    }
                });
            });
            
            $("body").on("click",".amount_refund",function(e){
                var id = $(this).attr("id");
                var user_id = $(this).attr("user_id");
                $("#user_id").val(user_id);
                $("#order_id").val(id);
                $("#refund_message").modal("show");
            });

            $("body").on("click",".btn_save_refund",function(e){
                var _token = $('input#_token').val();
                var user_id = $('#user_id').val();
                var order_id = $('#order_id').val();
                var amount = $("#amount").val();
                var note = $("#note").val();
                $.ajax({
                    method:"POST",
                    url:'<?php echo url("save-transaction") ?>',
                    data:{id:order_id,amount:amount,user_id:user_id,note:note,_token:_token},
                    success:function(res){
                        if(res.status == true){
                            $.notify(res.message, 'success');
                            $("#refund_message").modal("hide");
                            table.ajax.reload( null, false );
                        }else{
                            $.notify(res.message, 'error');
                        }
                    },
                    error: function(err) {
                        $.notify('Error occurred while saving.', 'error');
                    }
                });
            });

            $("body").on("click",".btn_cancel",function(e){
                $("#custom_message").modal("hide");
                $("#delete_modal").modal("hide");

            });

            $("body").on("click",".delete_btn",function(e){
                delete_id = $(this).attr("id");
                $("#delete_modal").modal("show");
            });

            $("body").on("click",".delete_yes",function(e){
                var _token = $('input#_token').val();
                $.ajax({
                    method:"POST",
                    data:{id:delete_id,_token:_token},
                    url:'<?php echo url("/admin/order/delete") ?>',
                    success:function(res){
                        if(res.status){
                            $.notify(res.message, 'success');
                            table.ajax.reload( null, false );

                        }else{
                            $.notify(res.message, 'error');
                        }

                    }
                });

            });



            $('#dataTable tbody').on( 'click', 'tr', function () {
                if ( $(this).hasClass('selected') ) {
                    $(this).removeClass('selected');
                }
                else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            } );

        });

    </script>
@endsection
