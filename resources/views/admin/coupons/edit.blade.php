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
                                                    <label>Cart Min. Value</label>
                                                    <input type="number" class="form-control" name="min_price" id="min_price" value="{{$coupon->min_price}}" placeholder="Cart Min. Value" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Code</label>
                                                    <input type="text" class="form-control" name="code" id="code" value="{{$coupon->code}}" placeholder="Code" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Discount In %</label>
                                                    <input type="number" class="form-control" name="discount" id="discount" value="{{$coupon->discount}}" placeholder="Discount In %" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Max. Coupon Amount</label>
                                                    <input type="number" class="form-control" name="max_price_applay" id="max_price_applay" value="{{$coupon->max_price_applay}}" placeholder="Max. Coupon Amount" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Max. Time Coupon Could be used by User</label>
                                                    <input type="number" class="form-control" name="no_of_time" id="no_of_time" value="{{$coupon->no_of_time}}" placeholder="Max. Time Coupon Could be used by User" required>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
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
        $('#title').val('');
        $('#price').val('');
        $('#code').val('');
    }
</script>
@endsection