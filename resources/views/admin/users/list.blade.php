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
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Image</th>
                                        <th>Status</th>
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
                    "url": '<?php echo url("/users-list") ?>',
                    "type": "GET"
                },
                "columns": [

                    {data: "id", name: "id", "visible": false},
                    {data: "first_name", name: "first_name"},
                    {data: "last_name", name: "last_name"},
                    {data: "email", name: "email"},
                    {
                        "name": "user_image",
                        "data": "user_image",
                        "render": function (data, type, full, meta) {
                            //alert(data);
                            if(data == '')
                            return `<div class='list-info mrg-top-10'><img class='thumb-img' src='https://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image' alt=''><div class='info'><span class='title'></span>
<span class='sub-title'></span></div></div>`
                            else
                                return `<div class='list-info mrg-top-10'><img class='thumb-img' src='${base_url+"/users/"+data}' alt=''><div class='info'><span class='title'></span>
<span class='sub-title'></span></div></div>`
                        },
                        "title": "Image",
                        "orderable": true,
                        "searchable": true
                    },
                    {data: 'delete', name: 'delete', orderable: false, searchable: false},
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

            $("body").on("click",".amount_refund",function(e){
                var id = $(this).attr("id");
                $("#user_id").val(id);
                $("#refund_message").modal("show");
            });

            $("body").on("click",".btn_save_refund",function(e){
                var _token = $('input#_token').val();
                var user_id = $('#user_id').val();
                var order_id = 0;
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
                    url:'<?php echo url("/admin/product/delete") ?>',
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
