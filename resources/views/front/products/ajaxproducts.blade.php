@if(count($products) > 0)
    @foreach($products as $row)
        <a href="{{url('product/detail/'.$row->slug)}}"><p product_slug='{{$row->slug}}' product_title='{{$row->product_title}}'>{{$row->product_title}}</p></a>
    @endforeach
@else
    <p>No matches found</p>
@endif