<div class="row">
    <div class="col-md-12">
        <label>Select City</label>
        <div class="mrg-top-10">
            <select class="selectize-group" name="listing_city">
                <option value="">Select City</option>
                @if(count($cities) > 0)
                    @foreach($cities as $row)
                        <option value="{{$row->id}}">{{$row->name}}</option>
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