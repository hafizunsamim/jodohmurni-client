@foreach($content['paragraphs'] as $idx => $paragraph)
  <p>{!! nl2br(e($paragraph)) !!}</p>
  @if($idx === 1)
    <p><a href="{{ $content['link'] }}">{{ $content['link'] }}</a></p>
  @endif
@endforeach
