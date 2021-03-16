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
                                                    <label>Delivery Date</label>
                                                    <input type="text" class="form-control datepicker-1" name="dilivery_date" id="dilivery_date" value="" placeholder="Title" readonly="" required>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <label>Status</label>
                                                <div class="mrg-top-10">
                                                    <select id="selectize-group" name="status" required="" >
                                                        <option value="Pending" <?php if($order->status == 'Pending'){ ?> selected="" <?php } ?> >Pending</option>
                                                        <option value="In Progress" <?php if($order->status == 'In Progress'){ ?> selected="" <?php } ?>>In Progress</option>
                                                        <option value="Delivered" <?php if($order->status == 'Delivered'){ ?> selected="" <?php } ?>>Delivered</option>    
                                                        <option value="Canceled" <?php if($order->status == 'Canceled'){ ?> selected="" <?php } ?>>Canceled</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <button type="submit" class="btn btn-primary">Update</button>
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