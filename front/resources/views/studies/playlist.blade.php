{{-- Video Playlist Page - v1.1 - Updated: 2026-01-29 --}}
@extends('layouts.app')

@section('title', $study->name . ' - ' . __('messages.videos'))

@section('content')
<div class="video-playlist-wrapper" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Main Video Player Section -->
            <div class="col-lg-8 video-main-section">
                <div class="video-player-container">
                    <!-- Breadcrumb -->
                    <div class="breadcrumb-bar bg-dark-1 px-40 py-20">
                        <div class="d-flex items-center justify-between">
                            <div class="d-flex items-center x-gap-10">
                                <a href="{{ route('accueil.index') }}" class="text-14 text-white opacity-70 hover-opacity-100">
                                    <i class="icon-home"></i> {{ __('messages.home') }}
                                </a>
                                <span class="text-14 text-white opacity-50">/</span>
                                <a href="{{ route('courses.show', $study->product->id) }}" class="text-14 text-white opacity-70 hover-opacity-100">
                                    {{ $study->product->titre }}
                                </a>
                                <span class="text-14 text-white opacity-50">/</span>
                                <span class="text-14 text-white fw-500">{{ $study->name }}</span>
                            </div>
                            <a href="{{ route('courses.show', $study->product->id) }}" class="btn btn-light btn-sm" style="background: white; color: #1e293b; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; font-size: 13px; font-weight: 500;">
                                <i class="icon-arrow-left mr-5" style="margin-right: 5px;"></i> {{ __('messages.back_to_course') }}
                            </a>
                        </div>
                    </div>

                    <!-- Video Player -->
                    <div class="main-video-wrapper bg-black">
                        <video
                            id="mainVideo"
                            controls
                            controlsList="nodownload"
                            class="main-video-player"
                            onended="playNextVideo()"
                            ontimeupdate="updateProgress()"
                            onplay="handleVideoPlay()">
                            <source id="mainVideoSource" src="" type="video/mp4">
                            {{ __('messages.video_not_supported') }}
                        </video>

                        <!-- Theater Mode Exit Button -->
                        <button class="theater-exit-btn" id="theaterExitBtn" onclick="toggleTheater()" style="display: none;">
                            <i class="icon-compress"></i>
                            <span>{{ __('messages.exit_theater_mode') }}</span>
                        </button>

                        <!-- Video Controls Overlay -->
                        <div class="video-controls-overlay" id="videoControlsOverlay">
                            <div class="d-flex justify-center items-center h-full">
                                <button class="play-overlay-btn" onclick="playCurrentVideo()">
                                    <i class="icon-play"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Video Info Bar -->
                    <div class="video-info-bar bg-white px-40 py-30">
                        <h1 class="text-24 fw-700 text-dark-1 mb-10" id="currentVideoTitle">{{ __('messages.select_video') }}</h1>
                        <div class="d-flex items-center justify-between flex-wrap">
                            <div class="d-flex items-center x-gap-30 mb-15">
                                <div class="d-flex items-center">
                                    <i class="icon-play text-purple-1 mr-10"></i>
                                    <span class="text-14 text-dark-1">{{ count($study->resource->videos) }} {{ __('messages.videos') }}</span>
                                </div>
                                <div class="d-flex items-center">
                                    <i class="icon-clock text-purple-1 mr-10"></i>
                                    <span class="text-14 text-dark-1" id="currentVideoDuration">--:--</span>
                                </div>
                                <div class="d-flex items-center">
                                    <i class="icon-calendar text-purple-1 mr-10"></i>
                                    <span class="text-14 text-dark-1" id="currentVideoDate">--</span>
                                </div>
                            </div>
                            <div class="d-flex items-center x-gap-15">
                                <button class="btn-action" onclick="toggleAutoplay()" id="autoplayBtn" title="{{ __('messages.autoplay') }}">
                                    <i class="icon-refresh"></i>
                                </button>
                                <button class="btn-action" onclick="toggleTheater()" id="theaterBtn" title="{{ __('messages.theater_mode') }}">
                                    <i class="icon-expand" id="theaterIcon"></i>
                                </button>
                                <button class="btn-action" onclick="sharePlaylist()" title="{{ __('messages.share') }}">
                                    <i class="icon-share"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress-bar-wrapper mt-20">
                            <div class="d-flex justify-between mb-10">
                                <span class="text-13 fw-500 text-dark-1">{{ __('messages.course_progress') }}</span>
                                <span class="text-13 fw-600 text-purple-1" id="overallProgress">0%</span>
                            </div>
                            <div class="progress-bar-track">
                                <div class="progress-bar-fill" id="progressBarFill" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Video Description -->
                    <div class="video-description bg-white px-40 py-30 border-top-light">
                        <h3 class="text-18 fw-600 text-dark-1 mb-15">{{ __('messages.about_this_course') }}</h3>
                        <p class="text-15 text-light-1 lh-lg">
                            {!! $study->product->description ? nl2br(e($study->product->description)) : __('messages.no_description_available') !!}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Playlist Sidebar -->
            <div class="col-lg-4 playlist-sidebar-section">
                <div class="playlist-sidebar bg-light-6">
                    <!-- Playlist Header -->
                    <div class="playlist-header bg-white px-30 py-25 border-bottom-light">
                        <div class="d-flex items-center justify-between mb-15">
                            <h2 class="text-20 fw-700 text-dark-1">{{ __('messages.playlist') }}</h2>
                            <button class="btn-sm-action" onclick="togglePlaylistSort()">
                                <i class="icon-sort"></i>
                            </button>
                        </div>
                        <div class="d-flex items-center justify-between">
                            <span class="text-13 text-light-1">
                                <span id="currentVideoIndex">0</span> / {{ count($study->resource->videos) }} {{ __('messages.videos') }}
                            </span>
                            <span class="text-13 fw-500 text-purple-1">
                                <span id="completedCount">0</span> {{ __('messages.completed') }}
                            </span>
                        </div>
                    </div>

                    <!-- Playlist Items -->
                    <div class="playlist-items">
                        @foreach($study->resource->videos as $index => $video)
                        <div class="playlist-item {{ $index === 0 ? 'active' : '' }}"
                             data-video-index="{{ $index }}"
                             data-video-url="{{ $study->resource->getVideoUrl($video['file'], $video['is_single'] ?? false) }}"
                             data-video-title="{{ $video['title'] }}"
                             data-video-date="{{ $video['uploaded_at'] }}"
                             data-is-single="{{ $video['is_single'] ?? 'false' }}"
                             onclick="loadVideo({{ $index }})">
                            <div class="playlist-item-inner">
                                <div class="d-flex items-start">
                                    <!-- Video Number / Status -->
                                    <div class="playlist-item-number">
                                        <div class="number-badge {{ $index === 0 ? 'active' : '' }}" id="badge-{{ $index }}">
                                            <span class="number">{{ $index + 1 }}</span>
                                            <i class="icon-check check-icon"></i>
                                            <i class="icon-play play-icon"></i>
                                        </div>
                                    </div>

                                    <!-- Video Thumbnail -->
                                    <div class="playlist-item-thumbnail">
                                        <div class="thumbnail-wrapper">
                                            <div class="thumbnail-placeholder bg-gradient-purple">
                                                <i class="icon-play text-white"></i>
                                            </div>
                                            <div class="thumbnail-duration" id="duration-{{ $index }}">--:--</div>
                                        </div>
                                    </div>

                                    <!-- Video Info -->
                                    <div class="playlist-item-info flex-1">
                                        <h4 class="playlist-item-title text-15 fw-500 text-dark-1 mb-5">
                                            {{ $video['title'] }}
                                        </h4>
                                        <div class="d-flex items-center x-gap-10">
                                            <span class="text-12 text-light-1">
                                                {{ \Carbon\Carbon::parse($video['uploaded_at'])->format('d/m/Y') }}
                                            </span>
                                            <span class="status-badge" id="status-{{ $index }}">
                                                {{ __('messages.not_watched') }}
                                            </span>
                                        </div>
                                        <!-- Progress for this video -->
                                        <div class="video-mini-progress mt-10">
                                            <div class="mini-progress-bar">
                                                <div class="mini-progress-fill" id="mini-progress-{{ $index }}" style="width: 0%;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="playlist-item-actions">
                                        <button class="btn-mini-action" onclick="event.stopPropagation(); downloadVideo({{ $index }})" title="{{ __('messages.download') }}">
                                            <i class="icon-download"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Playlist Footer -->
                    <div class="playlist-footer bg-white px-30 py-20 border-top-light" id="playlistFooter">
                        <button class="btn-primary w-full mb-10" onclick="playAll()">
                            <i class="icon-play mr-10"></i>
                            {{ __('messages.play_all') }}
                        </button>
                        <button class="btn-secondary w-full" id="resetBtn" onclick="resetPlaylist()">
                            <i class="icon-refresh mr-10"></i>
                            {{ __('messages.reset_progress') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Playlist Toggle -->
<button class="mobile-playlist-toggle" id="mobilePlaylistToggle" onclick="toggleMobilePlaylist()">
    <i class="icon-list"></i>
    <span class="badge">{{ count($study->resource->videos) }}</span>
</button>

<style>
/* CSS Variables */
:root {
    --purple-1: #0071ae;
    --purple-2: #005a8a;
    --dark-1: #1e293b;
    --light-1: #64748b;
    --light-6: #f8fafc;
    --white: #ffffff;
    --black: #000000;
}

/* Main Layout */
.video-playlist-wrapper {
    min-height: 100vh;
    background: var(--light-6);
}

.video-main-section {
    height: 100vh;
    overflow-y: auto;
}

.playlist-sidebar-section {
    height: 100vh;
    border-left: 1px solid #e2e8f0;
}

/* Video Player */
.main-video-wrapper {
    position: relative;
    width: 100%;
    aspect-ratio: 16 / 9;
    background: var(--black);
}

.main-video-player {
    width: 100%;
    height: 100%;
    display: block;
}

.video-controls-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
    z-index: 10;
}

.video-controls-overlay.show {
    opacity: 1;
    pointer-events: all;
}

.play-overlay-btn {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: rgba(0, 113, 174, 0.95);
    border: none;
    color: white;
    font-size: 40px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.play-overlay-btn:hover {
    transform: scale(1.1);
    background: var(--purple-1);
}

/* Video Info Bar */
.video-info-bar {
    border-bottom: 1px solid #e2e8f0;
}

.progress-bar-track {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--purple-1), var(--purple-2));
    border-radius: 3px;
    transition: width 0.3s ease;
}

/* Buttons */
.btn-action {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--light-6);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--dark-1);
}

.btn-action:hover {
    background: var(--purple-1);
    color: white;
    transform: scale(1.05);
}

.btn-action.active {
    background: var(--purple-1);
    color: white;
}

.btn-sm-action {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: transparent;
    border: 1px solid #e2e8f0;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-sm-action:hover {
    background: var(--purple-1);
    color: white;
    border-color: var(--purple-1);
}

.btn-primary {
    padding: 12px 24px;
    background: var(--purple-1);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-primary:hover {
    background: var(--purple-2);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0, 113, 174, 0.3);
}

.btn-secondary {
    padding: 12px 24px;
    background: transparent;
    color: var(--purple-1);
    border: 2px solid var(--purple-1);
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-secondary:hover {
    background: var(--purple-1);
    color: white;
}

/* Playlist Sidebar */
.playlist-sidebar {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.playlist-items {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

/* Playlist Item */
.playlist-item {
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 8px;
    overflow: hidden;
}

.playlist-item-inner {
    padding: 15px;
    background: white;
    border-radius: 8px;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.playlist-item:hover .playlist-item-inner {
    border-color: var(--purple-1);
    box-shadow: 0 4px 16px rgba(0, 113, 174, 0.15);
}

.playlist-item.active .playlist-item-inner {
    border-color: var(--purple-1);
    background: #f5f3ff;
}

.playlist-item.completed .playlist-item-inner {
    background: #f0fdf4;
}

/* Number Badge */
.playlist-item-number {
    margin-right: 12px;
}

.number-badge {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--light-6);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    color: var(--dark-1);
    position: relative;
    transition: all 0.3s ease;
}

.number-badge.active {
    background: var(--purple-1);
    color: white;
}

.number-badge.completed {
    background: #10b981;
    color: white;
}

.number-badge .check-icon,
.number-badge .play-icon {
    display: none;
    position: absolute;
}

.number-badge.active .play-icon {
    display: block;
}

.number-badge.active .number {
    display: none;
}

.number-badge.completed .check-icon {
    display: block;
}

.number-badge.completed .number {
    display: none;
}

/* Thumbnail */
.playlist-item-thumbnail {
    margin-right: 15px;
}

.thumbnail-wrapper {
    width: 80px;
    height: 45px;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
}

.thumbnail-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--purple-1), var(--purple-2));
}

.thumbnail-duration {
    position: absolute;
    bottom: 4px;
    right: 4px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 600;
}

/* Status Badge */
.status-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.not-watched {
    background: #f1f5f9;
    color: #64748b;
}

.status-badge.watching {
    background: #fef3c7;
    color: #d97706;
}

.status-badge.completed {
    background: #d1fae5;
    color: #10b981;
}

/* Mini Progress */
.mini-progress-bar {
    height: 3px;
    background: #e2e8f0;
    border-radius: 2px;
    overflow: hidden;
}

.mini-progress-fill {
    height: 100%;
    background: var(--purple-1);
    transition: width 0.3s ease;
}

/* Actions */
.btn-mini-action {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: var(--light-6);
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-mini-action:hover {
    background: var(--purple-1);
    color: white;
}

/* Mobile Playlist Toggle */
.mobile-playlist-toggle {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--purple-1);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 8px 24px rgba(0, 113, 174, 0.4);
    z-index: 1000;
    transition: all 0.3s ease;
}

.mobile-playlist-toggle:hover {
    transform: scale(1.1);
}

.mobile-playlist-toggle .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 24px;
    height: 24px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Icons */
.icon-play::before { content: "▶"; }
.icon-check::before { content: "✓"; }
.icon-download::before { content: "⬇"; }
.icon-share::before { content: "↗"; }
.icon-expand::before { content: "⛶"; }
.icon-compress::before { content: "⛶"; transform: rotate(180deg); display: inline-block; }
.icon-refresh::before { content: "↻"; }
.icon-list::before { content: "☰"; }
.icon-clock::before { content: "🕐"; }
.icon-calendar::before { content: "📅"; }
.icon-sort::before { content: "⇅"; }
.icon-home::before { content: "🏠"; }
.icon-arrow-left::before { content: "←"; }

/* Responsive */
@media (max-width: 991px) {
    .video-main-section,
    .playlist-sidebar-section {
        height: auto;
    }

    .playlist-sidebar-section {
        position: fixed;
        top: 0;
        right: -100%;
        width: 90%;
        max-width: 400px;
        height: 100vh;
        z-index: 9999;
        transition: right 0.3s ease;
        border-left: none;
        box-shadow: -4px 0 24px rgba(0, 0, 0, 0.2);
    }

    .playlist-sidebar-section.show {
        right: 0;
    }

    .mobile-playlist-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .main-video-wrapper {
        aspect-ratio: 16 / 9;
    }
}

@media (max-width: 576px) {
    .video-info-bar {
        padding: 20px;
    }

    .video-description {
        padding: 20px;
    }

    .playlist-header,
    .playlist-footer {
        padding: 20px;
    }

    .playlist-item-thumbnail {
        display: none;
    }
}

/* Theater Mode */
body.theater-mode {
    overflow: hidden;
}

body.theater-mode .main-video-wrapper {
    height: 100vh;
    aspect-ratio: auto;
}

body.theater-mode .video-info-bar,
body.theater-mode .video-description {
    display: none;
}

body.theater-mode .breadcrumb-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

body.theater-mode .breadcrumb-bar:hover {
    opacity: 1;
}

/* Theater Exit Button */
.theater-exit-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 1001;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 8px;
}

.theater-exit-btn:hover {
    background: rgba(0, 113, 174, 0.95);
    border-color: rgba(255, 255, 255, 0.6);
    transform: scale(1.05);
}

body.theater-mode .theater-exit-btn {
    display: flex !important;
}

/* Scrollbar Styling */
.playlist-items::-webkit-scrollbar {
    width: 6px;
}

.playlist-items::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.playlist-items::-webkit-scrollbar-thumb {
    background: var(--purple-1);
    border-radius: 3px;
}

.playlist-items::-webkit-scrollbar-thumb:hover {
    background: var(--purple-2);
}

/* Utility Classes */
.w-full { width: 100%; }
.h-full { height: 100%; }
.flex-1 { flex: 1; }
.border-top-light { border-top: 1px solid #e2e8f0; }
.border-bottom-light { border-bottom: 1px solid #e2e8f0; }
.opacity-70 { opacity: 0.7; }
.opacity-50 { opacity: 0.5; }
.hover-opacity-100:hover { opacity: 1; }
.bg-gradient-purple {
    background: linear-gradient(135deg, var(--purple-1), var(--purple-2));
}

/* Breadcrumb Styling */
.breadcrumb-bar {
    background: #1e293b !important;
}

.btn-light {
    background: white;
    color: var(--dark-1);
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    text-decoration: none;
}

.btn-light:hover {
    background: var(--light-6);
    transform: translateY(-1px);
}

.btn-sm {
    font-size: 13px;
    padding: 6px 12px;
}

.mr-5 { margin-right: 5px; }
.bg-dark-1 { background: var(--dark-1); }

</style>

<script>
// Global Variables
let currentVideoIndex = 0;
let isAutoplay = true;
let videoProgress = {};
let videos = @json($study->resource->videos);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializePlaylist();
    loadSavedProgress();
    loadVideo(0);
});

// Initialize playlist
function initializePlaylist() {
    videos.forEach((video, index) => {
        videoProgress[index] = {
            watched: false,
            progress: 0,
            duration: 0
        };
    });
}

// Load saved progress from localStorage
function loadSavedProgress() {
    const saved = localStorage.getItem('playlist_progress_{{ $study->id }}');
    if (saved) {
        videoProgress = JSON.parse(saved);
        updateProgressDisplay();
    }
}

// Save progress
function saveProgress() {
    localStorage.setItem('playlist_progress_{{ $study->id }}', JSON.stringify(videoProgress));
}

// Load video
function loadVideo(index) {
    currentVideoIndex = index;
    const video = videos[index];
    const mainVideo = document.getElementById('mainVideo');
    const mainVideoSource = document.getElementById('mainVideoSource');

    // Get video URL from data attribute
    const playlistItem = document.querySelector(`[data-video-index="${index}"]`);
    const videoUrl = playlistItem.getAttribute('data-video-url');

    // Update video source
    mainVideoSource.src = videoUrl;
    mainVideo.load();

    // Update video info
    document.getElementById('currentVideoTitle').textContent = video.title;
    document.getElementById('currentVideoDate').textContent = formatDate(video.uploaded_at);
    document.getElementById('currentVideoIndex').textContent = index + 1;

    // Update active state in playlist
    document.querySelectorAll('.playlist-item').forEach((item, i) => {
        item.classList.remove('active');
        const badge = document.getElementById(`badge-${i}`);
        badge.classList.remove('active');
        if (!videoProgress[i].watched) {
            badge.querySelector('.play-icon').style.display = 'none';
        }
    });

    const currentItem = document.querySelector(`[data-video-index="${index}"]`);
    currentItem.classList.add('active');
    const currentBadge = document.getElementById(`badge-${index}`);
    currentBadge.classList.add('active');
    currentBadge.querySelector('.play-icon').style.display = 'block';

    // Scroll to current video in playlist
    currentItem.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Show play overlay
    document.getElementById('videoControlsOverlay').classList.add('show');
}

// Play current video
function playCurrentVideo() {
    const mainVideo = document.getElementById('mainVideo');
    mainVideo.play();
    document.getElementById('videoControlsOverlay').classList.remove('show');
}

// Handle video play
function handleVideoPlay() {
    document.getElementById('videoControlsOverlay').classList.remove('show');
    updateVideoStatus(currentVideoIndex, 'watching');
}

// Update progress
function updateProgress() {
    const mainVideo = document.getElementById('mainVideo');
    if (mainVideo.duration > 0) {
        const progress = (mainVideo.currentTime / mainVideo.duration) * 100;

        // Update current video duration
        if (videoProgress[currentVideoIndex].duration === 0) {
            videoProgress[currentVideoIndex].duration = mainVideo.duration;
            document.getElementById('currentVideoDuration').textContent = formatTime(mainVideo.duration);
            document.getElementById(`duration-${currentVideoIndex}`).textContent = formatTime(mainVideo.duration);
        }

        // Update progress
        videoProgress[currentVideoIndex].progress = progress;
        document.getElementById(`mini-progress-${currentVideoIndex}`).style.width = progress + '%';

        // Mark as watched if > 90%
        if (progress > 90 && !videoProgress[currentVideoIndex].watched) {
            markAsWatched(currentVideoIndex);
        }

        saveProgress();
        updateOverallProgress();
    }
}

// Mark video as watched
function markAsWatched(index) {
    videoProgress[index].watched = true;
    updateVideoStatus(index, 'completed');

    const badge = document.getElementById(`badge-${index}`);
    badge.classList.add('completed');
    badge.classList.remove('active');

    const item = document.querySelector(`[data-video-index="${index}"]`);
    item.classList.add('completed');

    saveProgress();
    updateOverallProgress();
}

// Update video status
function updateVideoStatus(index, status) {
    const statusEl = document.getElementById(`status-${index}`);
    statusEl.className = 'status-badge ' + status;

    if (status === 'watching') {
        statusEl.textContent = '{{ __("messages.watching") }}';
    } else if (status === 'completed') {
        statusEl.textContent = '{{ __("messages.completed") }}';
    } else {
        statusEl.textContent = '{{ __("messages.not_watched") }}';
    }
}

// Update overall progress
function updateOverallProgress() {
    const watchedCount = Object.values(videoProgress).filter(v => v.watched).length;
    const totalCount = videos.length;
    const percentage = (watchedCount / totalCount) * 100;

    document.getElementById('progressBarFill').style.width = percentage + '%';
    document.getElementById('overallProgress').textContent = Math.round(percentage) + '%';
    document.getElementById('completedCount').textContent = watchedCount;
}

// Play next video
function playNextVideo() {
    if (currentVideoIndex < videos.length - 1 && isAutoplay) {
        loadVideo(currentVideoIndex + 1);
        setTimeout(() => {
            playCurrentVideo();
        }, 1000);
    } else {
        // Show completion
        if (currentVideoIndex === videos.length - 1) {
            alert('{{ __("messages.playlist_completed") }}');
        }
    }
}

// Play all videos
function playAll() {
    loadVideo(0);
    setTimeout(() => {
        playCurrentVideo();
    }, 500);
}

// Download video
function downloadVideo(index) {
    const video = videos[index];
    const playlistItem = document.querySelector(`[data-video-index="${index}"]`);
    const videoUrl = playlistItem.getAttribute('data-video-url');

    const link = document.createElement('a');
    link.href = videoUrl;
    link.download = `${video.title}.mp4`;
    link.click();
}

// Toggle autoplay
function toggleAutoplay() {
    isAutoplay = !isAutoplay;
    const btn = document.getElementById('autoplayBtn');
    btn.classList.toggle('active');
}

// Toggle playlist sort
let sortOrder = 'default'; // 'default', 'ascending', 'descending'
function togglePlaylistSort() {
    const playlistItems = document.querySelector('.playlist-items');
    const items = Array.from(playlistItems.querySelectorAll('.playlist-item'));

    // Cycle through sort orders
    if (sortOrder === 'default') {
        sortOrder = 'ascending';
    } else if (sortOrder === 'ascending') {
        sortOrder = 'descending';
    } else {
        sortOrder = 'default';
    }

    // Sort items
    if (sortOrder === 'ascending') {
        items.sort((a, b) => {
            const titleA = a.getAttribute('data-video-title').toLowerCase();
            const titleB = b.getAttribute('data-video-title').toLowerCase();
            return titleA.localeCompare(titleB);
        });
    } else if (sortOrder === 'descending') {
        items.sort((a, b) => {
            const titleA = a.getAttribute('data-video-title').toLowerCase();
            const titleB = b.getAttribute('data-video-title').toLowerCase();
            return titleB.localeCompare(titleA);
        });
    } else {
        // Default order by index
        items.sort((a, b) => {
            return parseInt(a.getAttribute('data-video-index')) - parseInt(b.getAttribute('data-video-index'));
        });
    }

    // Re-append sorted items
    items.forEach(item => playlistItems.appendChild(item));

    // Show feedback
    const sortBtn = document.querySelector('[onclick="togglePlaylistSort()"]');
    if (sortOrder === 'default') {
        sortBtn.style.opacity = '1';
        sortBtn.title = '{{ __('messages.sort_by_name') }}';
    } else if (sortOrder === 'ascending') {
        sortBtn.style.opacity = '1';
        sortBtn.title = '{{ __('messages.sort_ascending') }}';
    } else {
        sortBtn.style.opacity = '1';
        sortBtn.title = '{{ __('messages.sort_descending') }}';
    }
}

// Toggle theater mode
function toggleTheater() {
    document.body.classList.toggle('theater-mode');
    const theaterIcon = document.getElementById('theaterIcon');
    const theaterBtn = document.getElementById('theaterBtn');
    const theaterExitBtn = document.getElementById('theaterExitBtn');

    if (document.body.classList.contains('theater-mode')) {
        theaterIcon.className = 'icon-compress';
        theaterBtn.title = '{{ __('messages.exit_theater_mode') }}';
        theaterBtn.classList.add('active');
        if (theaterExitBtn) theaterExitBtn.style.display = 'flex';
    } else {
        theaterIcon.className = 'icon-expand';
        theaterBtn.title = '{{ __('messages.theater_mode') }}';
        theaterBtn.classList.remove('active');
        if (theaterExitBtn) theaterExitBtn.style.display = 'none';
    }
}

// Share playlist
function sharePlaylist() {
    const url = window.location.href;
    if (navigator.share) {
        navigator.share({
            title: '{{ $study->name }}',
            text: '{{ __("messages.check_out_this_playlist") }}',
            url: url
        });
    } else {
        navigator.clipboard.writeText(url);
        alert('{{ __("messages.link_copied") }}');
    }
}

// Reset playlist
function resetPlaylist() {
    if (confirm('{{ __("messages.reset_confirm") }}')) {
        localStorage.removeItem('playlist_progress_{{ $study->id }}');
        location.reload();
    }
}

// Toggle mobile playlist
function toggleMobilePlaylist() {
    document.querySelector('.playlist-sidebar-section').classList.toggle('show');
}

// Format time
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('{{ app()->getLocale() }}', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    const mainVideo = document.getElementById('mainVideo');

    switch(e.key) {
        case ' ': // Space - Play/Pause
            e.preventDefault();
            if (mainVideo.paused) {
                playCurrentVideo();
            } else {
                mainVideo.pause();
            }
            break;
        case 'ArrowRight': // Next video
            e.preventDefault();
            if (currentVideoIndex < videos.length - 1) {
                loadVideo(currentVideoIndex + 1);
            }
            break;
        case 'ArrowLeft': // Previous video
            e.preventDefault();
            if (currentVideoIndex > 0) {
                loadVideo(currentVideoIndex - 1);
            }
            break;
        case 'f': // Fullscreen
            e.preventDefault();
            if (mainVideo.requestFullscreen) {
                mainVideo.requestFullscreen();
            }
            break;
    }
});
</script>
@endsection

