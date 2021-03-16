<?php /*?>@if (count($errors) > 0)
    <!-- Form Error List -->
    <div class="alert alert-danger">
        <strong>Whoops! Something went wrong!</strong>

        <br><br>

        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<?php */?>

@if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissable" style="margin:10px auto; width: 100%;">
    <button type="button" class="close close-sm" data-dismiss="alert">
                <i class="fa fa-times"></i>
            </button>
        
            @foreach ($errors->all() as $error)
            <p style="margin-bottom: 0px;">{{ $error }}</p>
            @endforeach
        
    </div>
@endif
