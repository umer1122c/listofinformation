<div class="row">
    <div class="col-md-12">
        <label>Sub Category</label>
        <div class="mrg-top-10">
            <select class="selectize-group" name="sub_cat_id">
                <option value="">Select Sub Category</option>
                @if(count($categories) > 0)
                    @foreach($categories as $row)
                        <option value="{{$row->id}}">{{$row->title}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
</div>
<script>
    $('.selectize-group').selectize({
        sortField: 'text'
    });
</script>