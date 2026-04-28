@extends('layouts.app')

@section('content')
<div class="container py-3">
  <h3 class="fw-bold mb-3" style="color:rgb(10, 126, 62);">Link Ebook Subscription</h3>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('warning'))
    <div class="alert alert-warning">{{ session('warning') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body">
      @foreach($content['paragraphs'] as $idx => $paragraph)
        <p>{!! nl2br(e($paragraph)) !!}</p>
        {{-- Monogami: banyak perenggan — pautan selepas perenggan kedua (idx 1). Poligami: satu perenggan sahaja — pautan selepas idx 0. --}}
        @if(!empty($content['link']) && ($idx === 1 || (count($content['paragraphs']) === 1 && $idx === 0)))
          <p class="mb-3">
            <a href="{{ $content['link'] }}" target="_blank" rel="noopener noreferrer">{{ $content['link'] }}</a>
          </p>
        @endif
      @endforeach
    </div>
  </div>

  <div class="mt-3">
    <a href="{{ route('subscription.index') }}" class="btn btn-outline-secondary">Kembali ke Subscription</a>
  </div>
</div>
@endsection
