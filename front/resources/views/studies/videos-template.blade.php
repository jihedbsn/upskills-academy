@extends('layouts.app')

@section('title', $study->name . ' - ' . __('messages.videos'))

@section('content')
<main class="main-content">
    <header data-anim="fade" data-add-bg="" class="header -type-1 js-header">
        <div class="header__container">
            <div class="row justify-between items-center">
                <div class="col-auto">
                    <div class="header-left">
                        <div class="header__logo">
                            <a href="{{ route('accueil.index') }}">
                                <img src="{{ asset('assets/img/general/logo.svg') }}" alt="logo">
                            </a>
                        </div>
                        <div class="header__explore text-green-1 ml-60 xl:ml-30 lg:d-none">
                            <a href="#" class="d-flex items-center">
                                <i class="icon icon-explore mr-15"></i>
                                {{ __('messages.explore') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-auto lg:d-none">
                    <div class="text-20 lh-1 text-white fw-500">{{ $study->name }}</div>
                </div>

                <div class="col-auto">
                    <div class="header-right d-flex items-center">
                        <div class="header-right__buttons">
                            <a href="{{ route('courses.show', $study->product->id) }}" class="button -sm -rounded -white text-dark-1">
                                {{ __('messages.back_to_course') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="content-wrapper js-content-wrapper">
        <aside class="lesson-sidebar bg-light-3">
            <div class="px-30 sm:px-20">
                <form class="lesson-sidebar-search" action="post">
                    <input type="text" placeholder="{{ __('messages.search') }}">
                    <button class="" type="submit">
                        <i class="icon-search text-20"></i>
                    </button>
                </form>

                <div class="accordion -block-2 text-left js-accordion mt-30">
                    <div class="accordion__item">
                        <div class="accordion__button py-20 px-30 bg-light-4">
                            <div class="d-flex items-center">
                                <div class="accordion__icon">
                                    <div class="icon" data-feather="chevron-down"></div>
                                    <div class="icon" data-feather="chevron-up"></div>
                                </div>
                                <span class="text-17 fw-500 text-dark-1">{{ __('messages.course_content') }}</span>
                            </div>
                        </div>

                        <div class="accordion__content">
                            <div class="accordion__content__inner px-30 py-30">
                                <div class="y-gap-30">
                                    @foreach($study->resource->videos as $index => $video)
                                    <div class="video-lesson-item" data-video-index="{{ $index }}">
                                        <div class="d-flex">
                                            <div class="d-flex justify-center items-center size-30 rounded-full mr-10" style="background-color: #0071ae;">
                                                <div class="icon-play text-9"></div>
                                            </div>

                                            <div class="flex-1">
                                                <div class="video-title">{{ $video['title'] }}</div>
                                                <div class="d-flex x-gap-20 items-center pt-5">
                                                    <button onclick="playVideo({{ $index }})" class="text-14 lh-1 underline bg-none border-none cursor-pointer" style="color: #0071ae;">
                                                        {{ __('messages.preview') }}
                                                    </button>
                                                    <span class="text-14 lh-1 underline video-duration" id="duration-{{ $index }}" style="color: #0071ae;">
                                                        {{ __('messages.loading') }}...
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <section class="layout-pt-lg layout-pb-lg lg:pt-40">
            <div class="container">
                <div class="row justify-end">
                    <div class="col-xxl-8 col-xl-7 col-lg-8">
                        <!-- Video Player Section -->
                        <div class="relative pt-40">
                            <div class="video-player-container bg-dark-1 rounded-16 overflow-hidden position-relative" id="main-video-container">
                                <video
                                    id="main-video-player"
                                    controls
                                    class="w-1/1"
                                    style="min-height: 400px;"
                                    poster="{{ asset('assets/img/lesson-single/1.png') }}">
                                    <source id="video-source" src="" type="video/mp4">
                                    {{ __('messages.video_not_supported') }}
                                </video>

                                <!-- Video Overlay -->
                                <div class="video-overlay" id="video-overlay">
                                    <div class="play-button" onclick="playMainVideo()">
                                        <div class="icon-play text-48 text-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Info Section -->
                        <div class="mt-60 lg:mt-40">
                            <div class="d-flex items-center justify-between mb-30">
                                <h4 class="text-18 fw-500">{{ __('messages.videos') }}</h4>
                                <div class="d-flex items-center x-gap-15">
                                    <button class="button -sm text-white" style="background-color: #0071ae; border-color: #0071ae;" onclick="downloadCurrentVideo()">
                                        <div class="icon-download text-16 mr-10"></div>
                                        {{ __('messages.download') }}
                                    </button>
                                    <button class="button -sm text-white" style="background-color: #0071ae; border-color: #0071ae;" onclick="shareCurrentVideo()">
                                        <div class="icon-share text-16 mr-10"></div>
                                        {{ __('messages.share') }}
                                    </button>
                                </div>
                            </div>

                            <!-- Current Video Info -->
                            <div class="bg-light-3 rounded-8 p-30 mb-30">
                                <h5 class="text-16 fw-600 text-dark-1 mb-10" id="current-video-title">{{ $study->resource->videos[0]['title'] ?? __('messages.select_video') }}</h5>
                                <p class="text-14 text-light-1 mb-15" id="current-video-date">
                                    @if(isset($study->resource->videos[0]['uploaded_at']))
                                        {{ __('messages.uploaded_on') }}: {{ \Carbon\Carbon::parse($study->resource->videos[0]['uploaded_at'])->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                                <div class="d-flex items-center x-gap-20">
                                    <div class="d-flex items-center">
                                        <div class="icon-play text-14 mr-10" style="color: #0071ae;"></div>
                                        <span class="text-14 text-dark-1">{{ __('messages.video') }} <span id="current-video-number">1</span> / {{ count($study->resource->videos) }}</span>
                                    </div>
                                    <div class="d-flex items-center">
                                        <div class="icon-clock text-14 text-light-1 mr-10"></div>
                                        <span class="text-14 text-light-1" id="current-video-duration">{{ __('messages.loading') }}...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Video List -->
                            <div class="video-list">
                                <h5 class="text-16 fw-600 text-dark-1 mb-20">{{ __('messages.all_videos') }} ({{ count($study->resource->videos) }})</h5>
                                <div class="row y-gap-20">
                                    @foreach($study->resource->videos as $index => $video)
                                    <div class="col-12">
                                        <div class="video-item-card bg-white rounded-8 p-20 border-1 border-light-3 cursor-pointer {{ $index === 0 ? 'active' : '' }}"
                                             onclick="switchVideo({{ $index }})"
                                             data-video-index="{{ $index }}">
                                            <div class="d-flex items-center">
                                                <div class="d-flex justify-center items-center size-40 rounded-full mr-15" style="background-color: #0071ae;">
                                                    <span class="text-14 fw-600 text-white">{{ $index + 1 }}</span>
                                                </div>
                                                <div class="flex-1">
                                                    <h6 class="text-15 fw-500 text-dark-1 mb-5">{{ $video['title'] }}</h6>
                                                    <p class="text-12 text-light-1">
                                                        {{ \Carbon\Carbon::parse($video['uploaded_at'])->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                                <div class="d-flex items-center x-gap-10">
                                                    <button class="action-btn" onclick="event.stopPropagation(); downloadVideo({{ $index }})" title="{{ __('messages.download') }}">
                                                        <div class="icon-download text-16 text-light-1"></div>
                                                    </button>
                                                    <button class="action-btn" onclick="event.stopPropagation(); shareVideo({{ $index }})" title="{{ __('messages.share') }}">
                                                        <div class="icon-share text-16 text-light-1"></div>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Custom Styles -->
<style>
.video-player-container {
    position: relative;
    background: #000;
}

.video-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
    transition: opacity 0.3s ease;
}

.video-overlay.hidden {
    opacity: 0;
    pointer-events: none;
}

.play-button {
    width: 80px;
    height: 80px;
    background: rgba(0, 113, 174, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.play-button:hover {
    background: #0071ae;
    transform: scale(1.1);
}

.video-item-card {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.video-item-card:hover {
    border-color: #0071ae;
    box-shadow: 0 4px 12px rgba(0, 113, 174, 0.15);
    transform: translateY(-2px);
}

.video-item-card.active {
    border-color: #0071ae;
    background: #f8fafc;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f1f5f9;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: #0071ae;
    color: white;
    transform: scale(1.1);
}

.video-lesson-item {
    transition: all 0.3s ease;
    padding: 10px;
    border-radius: 8px;
}

.video-lesson-item:hover {
    background: #f8fafc;
}

.video-lesson-item.active {
    background: #f0f4ff;
    border-left: 3px solid #0071ae;
}

/* Icons */
.icon-play::before { content: "▶"; }
.icon-download::before { content: "⬇"; }
.icon-share::before { content: "📤"; }
.icon-clock::before { content: "🕐"; }
.icon-search::before { content: "🔍"; }
</style>

<!-- JavaScript -->
<script>
// Global variables
let currentVideoIndex = 0;
let videos = @json($study->resource->videos);
let mainVideo = document.getElementById('main-video-player');
let videoSource = document.getElementById('video-source');
let videoOverlay = document.getElementById('video-overlay');

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    if (videos.length > 0) {
        loadVideo(0);
    }
});

// Load video
function loadVideo(index) {
    if (index >= 0 && index < videos.length) {
        currentVideoIndex = index;
        const video = videos[index];

        // Update video source
        videoSource.src = "{{ config('app.file_url') }}uploads/resources/video/" + video.file;
        mainVideo.load();

        // Update UI
        updateVideoInfo(index);
        updateSidebarActive(index);
        updateVideoListActive(index);
    }
}

// Update video info
function updateVideoInfo(index) {
    const video = videos[index];

    document.getElementById('current-video-title').textContent = video.title;
    document.getElementById('current-video-number').textContent = index + 1;
    document.getElementById('current-video-date').textContent =
        '{{ __("messages.uploaded_on") }}: ' + new Date(video.uploaded_at).toLocaleDateString();
}

// Update sidebar active state
function updateSidebarActive(index) {
    document.querySelectorAll('.video-lesson-item').forEach((item, i) => {
        item.classList.toggle('active', i === index);
    });
}

// Update video list active state
function updateVideoListActive(index) {
    document.querySelectorAll('.video-item-card').forEach((card, i) => {
        card.classList.toggle('active', i === index);
    });
}

// Play main video
function playMainVideo() {
    mainVideo.play();
    videoOverlay.classList.add('hidden');
}

// Play video from sidebar
function playVideo(index) {
    loadVideo(index);
    setTimeout(() => {
        playMainVideo();
    }, 100);
}

// Switch video
function switchVideo(index) {
    loadVideo(index);
}

// Download current video
function downloadCurrentVideo() {
    const video = videos[currentVideoIndex];
    const link = document.createElement('a');
    link.href = "{{ config('app.file_url') }}uploads/resources/video/" + video.file;
    link.download = video.title + '.mp4';
    link.click();
}

// Download specific video
function downloadVideo(index) {
    const video = videos[index];
    const link = document.createElement('a');
    link.href = "{{ config('app.file_url') }}uploads/resources/video/" + video.file;
    link.download = video.title + '.mp4';
    link.click();
}

// Share current video
function shareCurrentVideo() {
    shareVideo(currentVideoIndex);
}

// Share specific video
function shareVideo(index) {
    const video = videos[index];
    const url = window.location.href + '#video-' + index;

    if (navigator.share) {
        navigator.share({
            title: video.title,
            text: 'Regardez cette vidéo de cours',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url).then(() => {
            alert('{{ __("messages.link_copied") }}');
        });
    }
}

// Update video duration when loaded
mainVideo.addEventListener('loadedmetadata', function() {
    const duration = formatTime(mainVideo.duration);
    document.getElementById('current-video-duration').textContent = duration;

    // Update sidebar duration
    const sidebarDuration = document.getElementById('duration-' + currentVideoIndex);
    if (sidebarDuration) {
        sidebarDuration.textContent = duration;
    }
});

// Format time
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

// Handle video events
mainVideo.addEventListener('play', function() {
    videoOverlay.classList.add('hidden');
});

mainVideo.addEventListener('pause', function() {
    // Handle pause if needed
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === ' ') {
        e.preventDefault();
        if (mainVideo.paused) {
            mainVideo.play();
        } else {
            mainVideo.pause();
        }
    } else if (e.key === 'ArrowLeft') {
        e.preventDefault();
        if (currentVideoIndex > 0) {
            loadVideo(currentVideoIndex - 1);
        }
    } else if (e.key === 'ArrowRight') {
        e.preventDefault();
        if (currentVideoIndex < videos.length - 1) {
            loadVideo(currentVideoIndex + 1);
        }
    }
});
</script>
@endsection
