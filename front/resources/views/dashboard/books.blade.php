@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.my_books') }}</h4>
          </div>

          @if(empty($books))
            <div class="text-center py-50">
              <i class="text-60 icon-bookmark text-light-3 mb-20"></i>
              <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.no_books_yet') }}</h5>
              <p class="text-14 lh-1 text-light-1">{{ __('messages.access_library_message') }}</p>
              <a href="{{ route('accueil.index') }}" class="button -md -blue-1 text-white mt-20">
                {{ __('messages.access_books') }}
              </a>
            </div>
          @else
            <div class="row y-gap-30">
              @foreach($books as $book)
                <div class="col-lg-4 col-md-6">
                  <div class="coursesCard -type-1">
                    <div class="coursesCard__image">
                      <img src="{{ $book->cover }}" alt="{{ $book->title }}">
                      <div class="coursesCard__image_overlay">
                        <div class="coursesCard__level">{{ $book->category }}</div>
                      </div>
                    </div>
                    <div class="coursesCard__content">
                      <h5 class="coursesCard__title">
                        <a href="#">{{ $book->title }}</a>
                      </h5>
                      <p class="coursesCard__text">{{ $book->author }}</p>
                      <div class="coursesCard__footer">
                        <div class="coursesCard__footer_left">
                          <div class="coursesCard__rating">
                            <div class="coursesCard__rating_stars">
                              @for($i = 1; $i <= 5; $i++)
                                <i class="icon-star {{ $i <= $book->rating ? 'text-yellow-1' : 'text-light-3' }}"></i>
                              @endfor
                            </div>
                            <div class="coursesCard__rating_text">{{ $book->rating }}/5</div>
                          </div>
                        </div>
                        <div class="coursesCard__footer_right">
                          <a href="#" class="button -sm -blue-1 text-white">{{ __('messages.read') }}</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
