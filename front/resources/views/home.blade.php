@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="hero-section bg-primary text-white py-5 mb-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Welcome to Our Learning Platform</h1>
                    <p class="lead mb-4">Discover amazing courses and enhance your skills with our expert instructors.</p>
                    <a href="{{ route('courses.index') }}" class="btn btn-light btn-lg">Browse Courses</a>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-mortarboard display-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Courses -->
    @if($popularCourses->count() > 0)
    <div class="container mb-5">
        <h2 class="text-center mb-4">Popular Courses</h2>
        <div class="row">
            @foreach($popularCourses as $course)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $course->titre }}</h5>
                        <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">{{ $course->category->name_en ?? 'No Category' }}</span>
                            <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $course->id]) }}" class="btn btn-outline-primary btn-sm">View Course</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Categories -->
    @if($categories->count() > 0)
    <div class="container mb-5">
        <h2 class="text-center mb-4">Categories</h2>
        <div class="row">
            @foreach($categories as $category)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="bi bi-folder-fill fs-1 text-primary mb-3"></i>
                        <h6 class="card-title">{{ $category->name_en }}</h6>
                        <a href="{{ route('courses.search', ['category' => $category->id]) }}" class="btn btn-outline-primary btn-sm">Explore</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Articles -->
    @if(isset($recentArticles) && $recentArticles->count() > 0)
    <div class="container mb-5">
        <h2 class="text-center mb-4">Latest Articles</h2>
        <div class="row">
            @foreach($recentArticles as $article)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $article->titre }}</h5>
                        <p class="card-text">{{ Str::limit($article->description, 100) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">{{ $article->created_at->format('M d, Y') }}</small>
                            <a href="#" class="btn btn-outline-primary btn-sm">Read More</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
