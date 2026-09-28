@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.my_courses') }}</h4>
          </div>

          @if(empty($courses))
            <div class="text-center py-50">
              <i class="text-60 icon-play-button text-light-3 mb-20"></i>
              <h5 class="text-16 lh-1 fw-500 mb-10">{{ __('messages.no_courses_yet') }}</h5>
              <p class="text-14 lh-1 text-light-1">{{ __('messages.start_learning_message') }}</p>
              <a href="{{ route('accueil.index') }}" class="button -md -purple-1 text-white mt-20">
                {{ __('messages.browse_courses') }}
              </a>
            </div>
          @else
            <div class="row y-gap-30">
              @foreach($courses as $course)
                <div class="col-lg-4 col-md-6">
                  <div class="coursesCard -type-1">
                    <div class="coursesCard__image">
                      <img src="{{ $course->image }}" alt="{{ $course->title }}">
                      <div class="coursesCard__image_overlay">
                        <div class="coursesCard__level">{{ $course->level }}</div>
                      </div>
                    </div>
                    <div class="coursesCard__content">
                      <h5 class="coursesCard__title">
                        <a href="#">{{ $course->title }}</a>
                      </h5>
                      <p class="coursesCard__text">{{ $course->description }}</p>
                      <div class="coursesCard__footer">
                        <div class="coursesCard__footer_left">
                          <div class="coursesCard__rating">
                            <div class="coursesCard__rating_stars">
                              @for($i = 1; $i <= 5; $i++)
                                <i class="icon-star {{ $i <= $course->rating ? 'text-yellow-1' : 'text-light-3' }}"></i>
                              @endfor
                            </div>
                            <div class="coursesCard__rating_text">{{ $course->rating }}/5</div>
                          </div>
                        </div>
                        <div class="coursesCard__footer_right">
                          <div class="coursesCard__price">{{ $course->price }}</div>
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
