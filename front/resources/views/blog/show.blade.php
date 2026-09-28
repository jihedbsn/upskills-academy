@extends('layouts.app')

@section('title', $blog->titre . ' - ' . __('messages.swedish_academy'))

@section('styles')
<style>
  .blog-single__image img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
    border-radius: 8px;
  }

  .blog-single__category {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: #6440FB;
    letter-spacing: 1px;
    padding: 4px 12px;
    background: rgba(100, 64, 251, 0.1);
    border-radius: 20px;
  }

  .blog-single__title {
    font-size: 32px;
    font-weight: 700;
    line-height: 1.3;
    color: #242239;
  }

  .blog-single__content {
    line-height: 1.8;
    color: #374151;
  }

  .blog-single__content h2,
  .blog-single__content h3,
  .blog-single__content h4 {
    color: #242239;
    margin: 30px 0 15px 0;
  }

  .blog-single__content p {
    margin-bottom: 20px;
  }

  .blog-single__content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
  }

  .sidebar.-blog {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .sidebar__title {
    font-size: 20px;
    font-weight: 600;
    color: #242239;
    margin-bottom: 20px;
  }

  .blogCard.-type-2 {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: inherit;
    padding: 15px 0;
    border-bottom: 1px solid #E5E7EB;
    transition: all 0.3s ease;
  }

  .blogCard.-type-2:hover {
    background: #F9FAFB;
    border-radius: 8px;
    padding: 15px;
    margin: 0 -15px;
  }

  .blogCard.-type-2:last-child {
    border-bottom: none;
  }

  .blogCard.-type-2 .blogCard__image {
    flex-shrink: 0;
    width: 80px;
    height: 60px;
    margin-right: 15px;
  }

  .blogCard.-type-2 .blogCard__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 6px;
  }

  .blogCard.-type-2 .blogCard__content {
    flex: 1;
  }

  .blogCard.-type-2 .blogCard__category {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    color: #6440FB;
    letter-spacing: 1px;
  }

  .blogCard.-type-2 .blogCard__title {
    font-size: 14px;
    font-weight: 600;
    line-height: 1.4;
    color: #242239;
    margin: 5px 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  .blogCard.-type-2 .blogCard__date {
    font-size: 12px;
    color: #9CA3AF;
  }

  @media (max-width: 768px) {
    .blog-single__title {
      font-size: 24px;
    }
    
    .sidebar.-blog {
      margin-top: 30px;
    }
  }
</style>
@endsection

@section('content')
<section class="layout-pt-lg layout-pb-lg">
  <div data-anim-wrap class="container">
    <div class="row y-gap-30">
      <!-- Contenu principal -->
      <div class="col-lg-8">
        <div data-anim-child="slide-left delay-1">
          <!-- Image du blog -->
          <div class="blog-single__image mb-30">
            <img src="{{ $blog->image ? config('app.file_url') . 'uploads/blogs/' . $blog->image : asset('assets/img/placeholders/course-not-found.png') }}" 
                 alt="{{ $blog->titre }}"
                 onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
          </div>

          <!-- En-tête du blog -->
          <div class="blog-single__header mb-30">
            <div class="d-flex items-center mb-15">
              <div class="blog-single__category">{{ __('messages.education') }}</div>
              <div class="blog-single__date text-14 text-light-1 ml-20">
                {{ $blog->published_date ? $blog->published_date->format('F d, Y') : $blog->created_at->format('F d, Y') }}
              </div>
            </div>
            
            <h1 class="blog-single__title text-30 lh-13 fw-500 text-dark-1 mb-15">{{ $blog->titre }}</h1>
            
            <div class="d-flex items-center">
              <div class="d-flex items-center text-14 text-light-1">
                <i class="icon-eye text-13 mr-5"></i>
                {{ $blog->views_count }} {{ __('messages.views') }}
              </div>
              @if($blog->author)
                <div class="d-flex items-center text-14 text-light-1 ml-20">
                  <i class="icon-user text-13 mr-5"></i>
                  {{ $blog->author }}
                </div>
              @endif
            </div>
          </div>

          <!-- Contenu du blog -->
          <div class="blog-single__content">
            <div class="text-15 lh-1-7 text-dark-1">
              {!! $blog->description !!}
            </div>
          </div>

          <!-- Tags et partage -->
          <div class="blog-single__footer mt-40 pt-30 border-top-light">
            <div class="row y-gap-20 justify-between items-center">
              <div class="col-auto">
                <div class="d-flex items-center">
                  <span class="text-15 fw-500 text-dark-1 mr-15">{{ __('messages.share') }}:</span>
                  <div class="d-flex items-center">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="d-flex items-center justify-center size-40 rounded-full bg-light-3 -hover-bg-purple-1 -hover-text-white text-15 text-dark-1 mr-10">
                      <i class="icon-facebook"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($blog->titre) }}" target="_blank" class="d-flex items-center justify-center size-40 rounded-full bg-light-3 -hover-bg-purple-1 -hover-text-white text-15 text-dark-1 mr-10">
                      <i class="icon-twitter"></i>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" class="d-flex items-center justify-center size-40 rounded-full bg-light-3 -hover-bg-purple-1 -hover-text-white text-15 text-dark-1">
                      <i class="icon-linkedin"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div data-anim-child="slide-left delay-2">
          <!-- Articles récents -->
          <div class="sidebar -blog">
            <div class="sidebar__item">
              <h4 class="sidebar__title text-18 fw-500 text-dark-1 mb-25">{{ __('messages.recent_articles') }}</h4>
              
              <div class="sidebar__list">
                @forelse($recentBlogs as $recentBlog)
                  <div class="sidebar__item">
                    <a href="{{ route('blog.show', $recentBlog->slug) }}" class="blogCard -type-2">
                      <div class="blogCard__image">
                        <img src="{{ $recentBlog->image ? config('app.file_url') . 'uploads/blogs/' . $recentBlog->image : asset('assets/img/placeholders/course-not-found.png') }}" 
                             alt="{{ $recentBlog->titre }}"
                             onerror="this.src='{{ asset('assets/img/placeholders/course-not-found.png') }}'">
                      </div>
                      <div class="blogCard__content">
                        <div class="blogCard__category">{{ __('messages.education') }}</div>
                        <h4 class="blogCard__title">{{ $recentBlog->titre }}</h4>
                        <div class="blogCard__date">
                          {{ $recentBlog->published_date ? $recentBlog->published_date->format('M d, Y') : $recentBlog->created_at->format('M d, Y') }}
                        </div>
                      </div>
                    </a>
                  </div>
                @empty
                  <div class="text-center py-20">
                    <p class="text-14 text-light-1">{{ __('messages.no_recent_articles') }}</p>
                  </div>
                @endforelse
              </div>
            </div>

            <!-- Retour au blog -->
            <div class="sidebar__item mt-30">
              <a href="{{ route('blog.index') }}" class="button -md -outline-purple-1 text-purple-1 w-100">
                <i class="icon-arrow-left text-13 mr-10"></i>
                {{ __('messages.back_to_blog') }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
