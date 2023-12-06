@for($i=1;$i<=5 ; $i++)
    <i class="fas fa-star {{$reviewRating >= $i ? 'filled':''}}"></i>
@endfor

@if($reviewsCount)
    <span class="d-inline-block average-rating">({{$reviewsCount}})</span>
@endif