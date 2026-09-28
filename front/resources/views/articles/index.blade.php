@extends('layouts.app')

@section('title', __('messages.articles') . ' - ' . __('messages.swedish_academy'))

@section('styles')
<style>
  .articleCard.-type-1 {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .articleCard.-type-1:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  .articleCard__image {
    position: relative;
    overflow: hidden;
  }

  .articleCard__image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .articleCard.-type-1:hover .articleCard__image img {
    transform: scale(1.05);
  }

  .articleCard__content {
    padding: 20px;
  }

  .articleCard__category {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: #0071ae;
    letter-spacing: 1px;
  }

  .articleCard__title {
    font-size: 18px;
    font-weight: 600;
    line-height: 1.4;
    color: #242239;
    margin: 10px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .articleCard__date {
    font-size: 14px;
    color: #9CA3AF;
  }

  .form-input {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
  }

  .form-input input {
    width: 350px;
    padding-block: 12px; padding-inline-start: 20px; padding-inline-end: 52px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease;
  }

  .form-input input:focus {
    outline: none;
    border-color: #0071ae;
  }

  .form-input button {
    position: absolute;
    inset-inline-end: 5px;
    width: 38px;
    height: 34px;
    min-width: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0071ae;
    border: none;
    border-radius: 6px;
    padding: 8px 12px;
    color: white;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .form-input button:hover {
    background: #005a8a;
  }

  /* RTL Support for Arabic */
  [dir="rtl"] .form-input input {
    padding: 12px 20px 12px 50px;
  }

  [dir="rtl"] .form-input button {
    right: auto;
    left: 5px;
  }

  @media (max-width: 768px) {
    .articleCard__title {
      font-size: 16px;
    }

    .articleCard__image img {
      height: 150px;
    }
  }
</style>
@endsection

@section('content')
<section class="layout-pt-lg layout-pb-lg">
  <div data-anim-wrap class="container">
    <div data-anim-child="slide-left delay-1" class="row y-gap-20 justify-between items-center">
      <div class="col-lg-6">
        <div class="sectionTitle">
          <h2 class="sectionTitle__title">{{ __('messages.articles') }}</h2>
          <p class="sectionTitle__text">{{ __('messages.articles_description') }}</p>
        </div>
      </div>

      <div class="col-auto">
        <form method="GET" action="{{ route('articles.index') }}" class="d-flex items-center">
          <div class="form-input">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_articles') }}" class="text-15 lh-11 text-dark-1">
            <button type="submit" class="us-search-btn">
              <i class="icon-search text-13"></i>
            </button>
          </div>
        </form>
      </div>
    </div>

    @if(request('search'))
      <div class="row y-gap-20 pt-20">
        <div class="col-12">
          <div class="d-flex items-center">
            <span class="text-15 text-dark-1">{{ __('messages.search_results_for') }}: "{{ request('search') }}"</span>
            <a href="{{ route('articles.index') }}" class="button -sm -outline-purple-1 text-purple-1 ml-20">
              {{ __('messages.clear_search') }}
            </a>
          </div>
        </div>
      </div>
    @endif

    <div class="row y-gap-30 pt-60">
      @forelse($articles as $article)
        <div class="col-lg-4 col-md-6" data-anim-child="slide-left delay-{{ $loop->iteration + 1 }}">
          <a href="{{ route('articles.show', $article->slug) }}" class="articleCard -type-1">
            <div class="articleCard__image">
              <img src="{{ $article->image ? config('app.file_url') . 'uploads/articles/' . $article->image : asset('assets/img/placeholders/course-not-found.png') }}"
                   alt="{{ $article->titre }}"
                   onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
            </div>
            <div class="articleCard__content mt-20">
              <div class="articleCard__category">{{ __('messages.news') }}</div>
              <h4 class="articleCard__title text-17 lh-15 mt-10">{{ $article->titre }}</h4>
              <div class="articleCard__date text-14 mt-5">
                {{ $article->published_date ? $article->published_date->format('F d, Y') : $article->created_at->format('F d, Y') }}
              </div>
              @if($article->description_short)
                <p class="text-14 text-light-1 mt-10">{{ Str::limit($article->description_short, 100) }}</p>
              @endif
              <div class="d-flex items-center mt-15">
                <div class="d-flex items-center text-13 text-light-1">
                  <i class="icon-eye text-13 mr-5"></i>
                  {{ $article->views_count }} {{ __('messages.views') }}
                </div>
                @if($article->author)
                  <div class="d-flex items-center text-13 text-light-1 ml-20">
                    <i class="icon-user text-13 mr-5"></i>
                    {{ $article->author }}
                  </div>
                @endif
              </div>
            </div>
          </a>
        </div>
      @empty
        <div class="col-12" data-anim-child="slide-left delay-2">
          <div class="text-center py-60">
            <div class="icon-news text-60 text-light-1 mb-20"></div>
            <h3 class="text-20 fw-500 text-dark-1 mb-10">{{ __('messages.no_articles_found') }}</h3>
            <p class="text-15 text-light-1">{{ __('messages.no_articles_description') }}</p>
          </div>
        </div>
      @endforelse
    </div>

    @if($articles->hasPages())
      <div class="row y-gap-30 pt-60">
        <div class="col-12">
          <div class="d-flex justify-center">
            {{ $articles->appends(request()->query())->links() }}
          </div>
        </div>
      </div>
    @endif
  </div>
</section>
@endsection
