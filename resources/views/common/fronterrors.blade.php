<style>
.alert-danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
}
.alert {
    padding: 15px;
        padding-right: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.close{
	padding:5px 9px;
	float:right;
}
</style>
@if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissable" style="margin:10px auto;">
     <button type="button" class="close close-sm" data-dismiss="alert"><i class="fa fa-times"></i></button>
              <!-- <i class="fa fa-times"></i>-->
            @foreach ($errors->all() as $error)
                <p style="line-height: 20px; margin: 5px 0; color:#a94442;">{{ $error }}</p>
            @endforeach
    </div>
@endif

