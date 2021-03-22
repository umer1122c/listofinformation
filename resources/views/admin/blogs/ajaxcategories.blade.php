
    <div class="col-md-12">
        <label>Sub Category</label>
        <div class="mrg-top-10">
            <select class="selectize-group1" name="cat_id">
                <option value="">Select Child Category</option>
                @if(count($categories) > 0)
                    @foreach($categories as $row)
                        <option value="{{$row->cat_id}}">{{$row->cat_title}}</option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

<script>
    $('.selectize-group1').selectize({
        sortField: 'text'
    });
</script>