@extends('layouts.app')

@section('title', __('messages.blog') . ' - ' . __('messages.swedish_academy'))

@section('styles')
<style>
  .blogCard.-type-1 {
    display: block;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .blogCard.-type-1:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  }

  .blogCard__image {
    position: relative;
    overflow: hidden;
  }

  .blogCard__image img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
  }

  .blogCard.-type-1:hover .blogCard__image img {
    transform: scale(1.05);
  }

  .blogCard__content {
    padding: 20px;
  }

  .blogCard__category {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: #297a7b;
    letter-spacing: 1px;
  }

  .blogCard__title {
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

  .blogCard__date {
    font-size: 14px;
    color: #9CA3AF;
  }

  .form-input {
    position: relative;
    display: flex;
    align-items: center;
  }

  .form-input input {
    padding-block: 12px; padding-inline-start: 20px; padding-inline-end: 52px;
    border: 2px solid #E5E7EB;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.3s ease;
  }

  .form-input input:focus {
    outline: none;
    border-color: #3cb4b0;
  }

  .form-input .us-search-btn {
    position: absolute;
    inset-inline-end: 6px;
    width: 38px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #093162;
    border: none;
    border-radius: 6px;
    padding: 0;
    color: #ffffff;
    cursor: pointer;
    transition: background 150ms ease;
  }

  .form-input .us-search-btn:hover {
    background: #081d36;
  }

  .form-input .us-search-btn i {
    color: #ffffff;
  }

  @media (max-width: 768px) {
    .blogCard__title {
      font-size: 16px;
    }
    
    .blogCard__image img {
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
          <h2 class="sectionTitle__title">{{ __('messages.blog') }}</h2>
          <p class="sectionTitle__text">{{ __('messages.blog_description') }}</p>
        </div>
      </div>

      <div class="col-auto">
        <form method="GET" action="{{ route('blog.index') }}" class="d-flex items-center">
          <div class="form-input">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('messages.search_blog') }}" class="text-15 lh-11 text-dark-1">
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
            <a href="{{ route('blog.index') }}" class="button -sm -outline-purple-1 text-purple-1 ml-20">
              {{ __('messages.clear_search') }}
            </a>
          </div>
        </div>
      </div>
    @endif

    <div class="row y-gap-30 pt-60">
      @forelse($blogs as $blog)
        <div class="col-lg-4 col-md-6" data-anim-child="slide-left delay-{{ $loop->iteration + 1 }}">
          <a href="{{ route('blog.show', $blog->slug) }}" class="blogCard -type-1">
            <div class="blogCard__image">
              <img src="{{ $blog->image ? config('app.file_url') . 'uploads/blogs/' . $blog->image : asset('assets/img/placeholders/course-not-found.png') }}" 
                   alt="{{ $blog->titre }}"
                   onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
            </div>
            <div class="blogCard__content mt-20">
              <div class="blogCard__category">{{ __('messages.education') }}</div>
              <h4 class="blogCard__title text-17 lh-15 mt-10">{{ $blog->titre }}</h4>
              <div class="blogCard__date text-14 mt-5">
                {{ $blog->published_date ? $blog->published_date->format('F d, Y') : $blog->created_at->format('F d, Y') }}
              </div>
              @if($blog->description_short)
                <p class="text-14 text-light-1 mt-10">{{ Str::limit($blog->description_short, 100) }}</p>
              @endif
              <div class="d-flex items-center mt-15">
                <div class="d-flex items-center text-13 text-light-1">
                  <i class="icon-eye text-13 mr-5"></i>
                  {{ $blog->views_count }} {{ __('messages.views') }}
                </div>
                @if($blog->author)
                  <div class="d-flex items-center text-13 text-light-1 ml-20">
                    <i class="icon-user text-13 mr-5"></i>
                    {{ $blog->author }}
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
            <h3 class="text-20 fw-500 text-dark-1 mb-10">{{ __('messages.no_blogs_found') }}</h3>
            <p class="text-15 text-light-1">{{ __('messages.no_blogs_description') }}</p>
          </div>
        </div>
      @endforelse
    </div>

    @if($blogs->hasPages())
      <div class="row y-gap-30 pt-60">
        <div class="col-12">
          <div class="d-flex justify-center">
            {{ $blogs->appends(request()->query())->links() }}
          </div>
        </div>
      </div>
    @endif
  </div>
</section>
@endsection
