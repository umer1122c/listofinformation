function PreviewImage() {
    var ext = $('#img-upload').val().split('.').pop().toLowerCase();
    if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            $('#img-upload').val('');
            $('#upload_logo_error').html('gif , png , jpg , jpeg are allowed.');
    } else {
        $('#upload_logo_error').html('');
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("img-upload").files[0]);
        oFReader.onload = function (oFREvent) {
        document.getElementById("uploadPreview").src = oFREvent.target.result;
        };
    }
}

function PreviewImage1() {
    var ext = $('#img-upload1').val().split('.').pop().toLowerCase();
    if($.inArray(ext, ['gif','png','jpg','jpeg']) == -1) {
            $('#img-upload1').val('');
            $('#upload_logo_error1').html('gif , png , jpg , jpeg are allowed.');
    } else {
        $('#upload_logo_error1').html('');
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("img-upload1").files[0]);
        oFReader.onload = function (oFREvent) {
        document.getElementById("uploadPreview1").src = oFREvent.target.result;
        };
    }
}