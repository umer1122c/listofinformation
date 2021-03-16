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
        height: 100px;
        width: 100px;
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
                <h4>Manage Contacts</h4>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-block">
                            <div class="clearfix">
<!--                                <div class="btn-group">
                                    <a href="javascript:void(0)"><button id="editable-sample_new" class="btn btn-primary add_new_record">Add New <i class="fa fa-plus"></i></button></a>
                                </div>-->
                            </div>
                            <div class="table-overflow">
                                <table  id="dataTable" class="table table-lg table-hover" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Subject</th>
                                        <th>Message</th>
                                        <th>Contact Date</th>
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
                    <h5 class="modal-title" id="modal_title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body alert_message">
                    <form class="form-horizontal mrg-top-40 pdd-right-30" id="artical_form" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label for="form-1-1" class="col-md-4 control-label">Attribute Name</label>
                            <div class="col-md-8">
                                <input type="hidden" value="0" name="id" class="form-control" id="record_id" placeholder="id">
                                <input type="text" class="form-control" name="attributeName" id="attributeName" placeholder="Attribute Name">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="form-1-1" class="col-md-4 control-label">Attribute Cost</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="attributeCost" id="attributeCost" placeholder="Attribute Cost">
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
                    "url": '<?php echo url("/contacts-list") ?>',
                    "type": "GET"
                },
                "columns": [

                    {data: "id", name: "id", "visible": false},
                    {data: "name", name: "name"},
                    {data: "email", name: "email"},
                    {data: "phone", name: "phone"},
                    {data: "subject", name: "subject"},
                    {data: "message", name: "message"},
                    {data: "created_at", name: "created_at"}
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
                let value = JSON.parse($(this).attr("data-value"));
                $("#attributeName").val(value.attributeName);
                $("#attributeCost").val(value.attributeCost);
                $("#record_id").val(value.id);
                 $("#custom_message").modal("show");
            });
            
            $("body").on("click",".add_new_record",function(e){
                $("#modal_title").text("Add New Record");
                $("#record_id").val(0);
                $("#custom_message").modal("show");
            });

            
            $("body").on("click",".btn_save",function(e){
                var _token = $('input#_token').val();
                var id = $("#record_id").val();
                var attributeName = $("#attributeName").val();
                var attributeCost = $("#attributeCost").val();
                $.ajax({
                    method:"POST",
                    url:'<?php echo url("save-product-attribute") ?>',
                    data:{id:id,product_id:"",attributeName:attributeName,attributeCost:attributeCost,_token:_token},
                    success:function(res){
                        if(res.status == true){
                            $.notify(res.message, 'success');
                            $("#custom_message").modal("hide");
                            table.ajax.reload( null, false );
                        }else{
                            $.notify(res.message, 'error');
                        }
                        $("#attributeName").val('');
                        $("#attributeCost").val('');
                        $("#record_id").val('');
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
                    url:'<?php echo url("/delete-product-attribute") ?>',
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
