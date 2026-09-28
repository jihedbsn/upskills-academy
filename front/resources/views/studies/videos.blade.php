@extends('layouts.app')

@section('title', $study->name . ' - ' . __('messages.videos'))

@section('content')
<div class="container py-60">
    <div class="row justify-center">
        <div class="col-xl-12">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav mb-40">
                <div class="d-flex items-center x-gap-10">
                    <a href="{{ route('accueil.index') }}" class="text-14 text-light-1">{{ __('messages.home') }}</a>
                    <span class="text-14 text-light-1">/</span>
                    <a href="{{ route('courses.show', $study->product->id) }}" class="text-14 text-light-1">{{ $study->product->titre }}</a>
                    <span class="text-14 text-light-1">/</span>
                    <span class="text-14 text-purple-1 fw-500">{{ $study->name }}</span>
                </div>
            </nav>

            <!-- Header Section -->
            <div class="video-header bg-white rounded-16 p-40 mb-40 shadow-2">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex items-center mb-20">
                            <div class="d-flex justify-center items-center size-60 rounded-full bg-purple-1 mr-20">
                                <div class="icon-play text-24 text-white"></div>
                            </div>
                            <div>
                                <h1 class="text-28 fw-700 text-dark-1 mb-5">{{ $study->name }}</h1>
                                <p class="text-16 text-light-1">{{ $study->product->titre }}</p>
                            </div>
                        </div>

                        @php
                            // Count all videos for header display
                            $totalVideosCount = count($study->resource->getAllLocalizedVideos());
                        @endphp
                        <div class="d-flex items-center x-gap-40 mb-20">
                            <div class="d-flex items-center">
                                <div class="icon-play text-16 text-purple-1 mr-10"></div>
                                <span class="text-14 text-dark-1 fw-500">{{ $totalVideosCount }} {{ __('messages.videos') }}</span>
                            </div>
                            <div class="d-flex items-center">
                                <div class="icon-clock text-16 text-purple-1 mr-10"></div>
                                <span class="text-14 text-dark-1 fw-500">{{ $study->duration ?? __('messages.estimated_time', ['time' => '5 min']) }}</span>
                            </div>
                            <div class="d-flex items-center">
                                <div class="icon-calendar text-16 text-purple-1 mr-10"></div>
                                <span class="text-14 text-dark-1 fw-500">{{ $study->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="progress-bar-container mb-20">
                            <div class="d-flex items-center justify-between mb-10">
                                <span class="text-14 text-dark-1 fw-500">{{ __('messages.progress') }}</span>
                                <span class="text-14 text-purple-1 fw-600" id="progress-text">0%</span>
                            </div>
                            <div class="progress-bar bg-light-4 rounded-4" style="height: 8px;">
                                <div class="progress-fill bg-purple-1 rounded-4" id="progress-fill" style="width: 0%; height: 100%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end">
                        <div class="video-actions">
                            <button class="button -md -purple-1 text-white mr-10" onclick="playAllVideos()">
                                <div class="icon-play text-16 mr-10"></div>
                                {{ __('messages.play_all') }}
                            </button>
                            <button class="button -md -outline-purple-1 text-purple-1" onclick="downloadAllVideos()">
                                <div class="icon-download text-16 mr-10"></div>
                                {{ __('messages.download_all') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Videos Section -->
            @php
                // Get all videos (multilingual filtered by locale + legacy)
                $allVideos = $study->resource->getAllLocalizedVideos();
            @endphp
            <div class="row y-gap-30">
                @foreach($allVideos as $index => $video)
                <div class="col-lg-6">
                    <div class="video-card bg-white rounded-16 p-30 shadow-2" data-video-index="{{ $index }}">
                        <!-- Video Header -->
                        <div class="d-flex items-center justify-between mb-20">
                            <div class="d-flex items-center">
                                <div class="video-number d-flex justify-center items-center size-40 rounded-full bg-purple-1 mr-15">
                                    <span class="text-14 fw-600 text-white">{{ $index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-18 fw-600 text-dark-1 mb-5">{{ $video['title'] }}</h3>
                                    <div class="d-flex items-center x-gap-15">
                                        <span class="text-12 text-light-1">{{ $video['uploaded_at'] ? \Carbon\Carbon::parse($video['uploaded_at'])->format('d/m/Y H:i') : '' }}</span>
                                        <span class="video-status text-12 text-green-1 fw-500" id="status-{{ $index }}">{{ __('messages.not_started') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="video-actions-mini">
                                <button class="action-btn" onclick="toggleFullscreen({{ $index }})" title="{{ __('messages.fullscreen') }}">
                                    <div class="icon-expand text-16 text-light-1"></div>
                                </button>
                            </div>
                        </div>

                        <!-- Video Player -->
                        @php
                            // Determine video path - multilingual videos are in /videos/ folder, legacy might be in /video/ or /videos/
                            $videoPath = isset($video['is_multilingual']) && $video['is_multilingual']
                                ? 'uploads/resources/videos/'
                                : (isset($video['is_single']) && $video['is_single'] ? 'uploads/resources/' : 'uploads/resources/videos/');
                        @endphp
                        <div class="video-player-container rounded-12 overflow-hidden mb-20 position-relative">
                            <video
                                id="video-{{ $index }}"
                                controls
                                class="w-1/1 video-player"
                                data-video-src="{{ config('app.file_url') }}{{ $videoPath }}{{ $video['file'] }}"
                                onloadedmetadata="updateVideoDuration({{ $index }})"
                                ontimeupdate="updateVideoProgress({{ $index }})"
                                onended="markVideoCompleted({{ $index }})"
                                onplay="markVideoStarted({{ $index }})">
                                <source src="{{ config('app.file_url') }}{{ $videoPath }}{{ $video['file'] }}" type="video/mp4">
                                {{ __('messages.video_not_supported') }}
                            </video>

                            <!-- Video Overlay -->
                            <div class="video-overlay" id="overlay-{{ $index }}">
                                <div class="play-button" onclick="playVideo({{ $index }})">
                                    <div class="icon-play text-48 text-white"></div>
                                </div>
                            </div>

                            <!-- Video Progress -->
                            <div class="video-progress-container">
                                <div class="video-progress bg-light-4" style="height: 4px;">
                                    <div class="video-progress-fill bg-purple-1" id="video-progress-{{ $index }}" style="width: 0%; height: 100%; transition: width 0.1s ease;"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Video Info -->
                        <div class="video-footer">
                            <div class="d-flex items-center justify-between">
                                <div class="d-flex items-center x-gap-20">
                                    <div class="d-flex items-center">
                                        <div class="icon-play text-14 text-purple-1 mr-10"></div>
                                        <span class="text-14 text-dark-1 fw-500">{{ __('messages.lesson') }} {{ $index + 1 }}</span>
                                    </div>
                                    <div class="d-flex items-center">
                                        <div class="icon-clock text-14 text-light-1 mr-10"></div>
                                        <span class="text-14 text-light-1" id="duration-{{ $index }}">{{ __('messages.loading') }}...</span>
                                    </div>
                                </div>
                                <div class="d-flex items-center x-gap-15">
                                    <button class="action-btn" onclick="shareVideo({{ $index }})" title="{{ __('messages.share') }}">
                                        <div class="icon-share text-16 text-light-1"></div>
                                    </button>
                                    <a href="{{ config('app.file_url') }}{{ $videoPath }}{{ $video['file'] }}"
                                       download="{{ $video['title'] }}.mp4"
                                       class="action-btn" title="{{ __('messages.download') }}">
                                        <div class="icon-download text-16 text-light-1"></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Navigation Section -->
            <div class="video-navigation bg-white rounded-16 p-30 shadow-2 mt-40">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="d-flex items-center">
                            <div class="icon-info text-20 text-purple-1 mr-15"></div>
                            <div>
                                <h4 class="text-16 fw-600 text-dark-1 mb-5">{{ __('messages.learning_progress') }}</h4>
                                <p class="text-14 text-light-1">{{ __('messages.complete_all_lessons') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 text-lg-end">
                        <div class="d-flex items-center justify-lg-end x-gap-15">
                            <button class="button -sm -outline-purple-1 text-purple-1" onclick="resetProgress()">
                                <div class="icon-refresh text-16 mr-10"></div>
                                {{ __('messages.reset_progress') }}
                            </button>
                            <a href="{{ route('courses.show', $study->product->id) }}"
                               class="button -md -purple-1 text-white">
                                <div class="icon-arrow-left text-16 mr-10"></div>
                                {{ __('messages.back_to_course') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completion Certificate -->
            <div class="completion-certificate bg-gradient-purple rounded-16 p-40 mt-30 text-center" id="completion-certificate" style="display: none;">
                <div class="d-flex justify-center items-center size-80 rounded-full bg-white mr-20 mx-auto mb-20">
                    <div class="icon-check text-32 text-purple-1"></div>
                </div>
                <h3 class="text-24 fw-700 text-white mb-15">{{ __('messages.congratulations') }}!</h3>
                <p class="text-16 text-white mb-30">{{ __('messages.all_videos_completed') }}</p>
                <button class="button -md -white text-purple-1" onclick="downloadCertificate()">
                    <div class="icon-download text-16 mr-10"></div>
                    {{ __('messages.download_certificate') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Variables CSS */
:root {
    --purple-1: #8b5cf6;
    --purple-2: #7c3aed;
    --light-1: #64748b;
    --light-4: #f1f5f9;
    --dark-1: #1e293b;
    --green-1: #10b981;
    --white: #ffffff;
}

/* Base Styles */
.video-card {
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
    position: relative;
    overflow: hidden;
}

.video-card:hover {
    border-color: var(--purple-1);
    box-shadow: 0 12px 40px rgba(139, 92, 246, 0.15);
    transform: translateY(-4px);
}

.video-header {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
    position: relative;
    overflow: hidden;
}

.video-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.shadow-2 {
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

/* Video Player Styles */
.video-player-container {
    background: #000;
    position: relative;
    border-radius: 12px;
    overflow: hidden;
}

.video-player-container video {
    width: 100%;
    height: auto;
    display: block;
    min-height: 200px;
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
    background: rgba(139, 92, 246, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.play-button:hover {
    background: var(--purple-1);
    transform: scale(1.1);
}

.video-progress-container {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.video-progress-fill {
    transition: width 0.1s ease;
}

/* Progress Bar */
.progress-bar-container {
    margin: 20px 0;
}

.progress-bar {
    background: var(--light-4);
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    background: linear-gradient(90deg, var(--purple-1), var(--purple-2));
    transition: width 0.3s ease;
}

/* Button Styles */
.button {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.button.-md {
    padding: 12px 24px;
}

.button.-sm {
    padding: 8px 16px;
    font-size: 12px;
}

.button.-purple-1 {
    background: var(--purple-1);
    color: white;
}

.button.-outline-purple-1 {
    background: transparent;
    color: var(--purple-1);
    border: 2px solid var(--purple-1);
}

.button.-white {
    background: white;
    color: var(--purple-1);
}

.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(139, 92, 246, 0.3);
}

.button.-outline-purple-1:hover {
    background: var(--purple-1);
    color: white;
}

/* Action Buttons */
.action-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--light-4);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.action-btn:hover {
    background: var(--purple-1);
    color: white;
    transform: scale(1.1);
}

/* Status Indicators */
.video-status {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.video-status.not-started {
    background: var(--light-4);
    color: var(--light-1);
}

.video-status.in-progress {
    background: #fef3c7;
    color: #d97706;
}

.video-status.completed {
    background: #d1fae5;
    color: var(--green-1);
}

/* Certificate */
.completion-certificate {
    background: linear-gradient(135deg, var(--purple-1) 0%, var(--purple-2) 100%);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

.bg-gradient-purple {
    background: linear-gradient(135deg, var(--purple-1) 0%, var(--purple-2) 100%);
}

/* Icons */
.icon-play::before { content: "▶"; }
.icon-clock::before { content: "🕐"; }
.icon-calendar::before { content: "📅"; }
.icon-download::before { content: "⬇"; }
.icon-arrow-left::before { content: "←"; }
.icon-expand::before { content: "⛶"; }
.icon-share::before { content: "📤"; }
.icon-check::before { content: "✓"; }
.icon-refresh::before { content: "🔄"; }
.icon-info::before { content: "ℹ"; }

/* Responsive */
@media (max-width: 768px) {
    .video-card {
        margin-bottom: 20px;
    }

    .button {
        padding: 10px 20px;
        font-size: 13px;
    }

    .video-player-container video {
        min-height: 150px;
    }

    .play-button {
        width: 60px;
        height: 60px;
    }
}
</style>

<!-- JavaScript -->
<script>
// Global variables
let videoProgress = {};
let totalVideos = {{ count($study->resource->videos) }};
let completedVideos = 0;

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeVideos();
    loadProgress();
});

// Initialize video players
function initializeVideos() {
    for (let i = 0; i < totalVideos; i++) {
        videoProgress[i] = {
            started: false,
            completed: false,
            progress: 0
        };
    }
}

// Load progress from localStorage
function loadProgress() {
    const savedProgress = localStorage.getItem('videoProgress_{{ $study->id }}');
    if (savedProgress) {
        videoProgress = JSON.parse(savedProgress);
        updateProgressDisplay();
    }
}

// Save progress to localStorage
function saveProgress() {
    localStorage.setItem('videoProgress_{{ $study->id }}', JSON.stringify(videoProgress));
}

// Update video duration
function updateVideoDuration(index) {
    const video = document.getElementById(`video-${index}`);
    const durationElement = document.getElementById(`duration-${index}`);
    const duration = formatTime(video.duration);
    durationElement.textContent = duration;
}

// Update video progress
function updateVideoProgress(index) {
    const video = document.getElementById(`video-${index}`);
    const progressFill = document.getElementById(`video-progress-${index}`);
    const progress = (video.currentTime / video.duration) * 100;

    videoProgress[index].progress = progress;
    progressFill.style.width = progress + '%';

    // Mark as started if more than 10% watched
    if (progress > 10 && !videoProgress[index].started) {
        markVideoStarted(index);
    }

    // Mark as completed if more than 90% watched
    if (progress > 90 && !videoProgress[index].completed) {
        markVideoCompleted(index);
    }

    updateProgressDisplay();
    saveProgress();
}

// Mark video as started
function markVideoStarted(index) {
    videoProgress[index].started = true;
    const statusElement = document.getElementById(`status-${index}`);
    statusElement.textContent = '{{ __("messages.in_progress") }}';
    statusElement.className = 'video-status text-12 text-orange-1 fw-500 in-progress';
}

// Mark video as completed
function markVideoCompleted(index) {
    if (!videoProgress[index].completed) {
        videoProgress[index].completed = true;
        completedVideos++;

        const statusElement = document.getElementById(`status-${index}`);
        statusElement.textContent = '{{ __("messages.completed") }}';
        statusElement.className = 'video-status text-12 text-green-1 fw-500 completed';

        // Check if all videos are completed
        if (completedVideos === totalVideos) {
            showCompletionCertificate();
        }
    }

    updateProgressDisplay();
    saveProgress();
}

// Update overall progress display
function updateProgressDisplay() {
    const totalProgress = (completedVideos / totalVideos) * 100;
    const progressFill = document.getElementById('progress-fill');
    const progressText = document.getElementById('progress-text');

    progressFill.style.width = totalProgress + '%';
    progressText.textContent = Math.round(totalProgress) + '%';
}

// Play specific video
function playVideo(index) {
    const video = document.getElementById(`video-${index}`);
    const overlay = document.getElementById(`overlay-${index}`);

    video.play();
    overlay.classList.add('hidden');
}

// Play all videos
function playAllVideos() {
    for (let i = 0; i < totalVideos; i++) {
        const video = document.getElementById(`video-${i}`);
        const overlay = document.getElementById(`overlay-${i}`);

        if (!videoProgress[i].completed) {
            video.play();
            overlay.classList.add('hidden');
            break; // Play first uncompleted video
        }
    }
}

// Download all videos
function downloadAllVideos() {
    for (let i = 0; i < totalVideos; i++) {
        const video = document.getElementById(`video-${i}`);
        const link = document.createElement('a');
        link.href = "{{ config('app.file_url') }}uploads/resources/video/" + videos[i].file;
        link.download = `{{ $study->name }} - Video ${i + 1}.mp4`;
        link.click();
    }
}

// Toggle fullscreen
function toggleFullscreen(index) {
    const video = document.getElementById(`video-${index}`);
    if (video.requestFullscreen) {
        video.requestFullscreen();
    } else if (video.webkitRequestFullscreen) {
        video.webkitRequestFullscreen();
    } else if (video.msRequestFullscreen) {
        video.msRequestFullscreen();
    }
}

// Share video
function shareVideo(index) {
    const video = document.getElementById(`video-${index}`);
    const url = window.location.href + `#video-${index}`;

    if (navigator.share) {
        navigator.share({
            title: '{{ $study->name }} - Video ' + (index + 1),
            text: 'Regardez cette vidéo de cours',
            url: url
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('{{ __("messages.link_copied") }}');
        });
    }
}

// Reset progress
function resetProgress() {
    if (confirm('{{ __("messages.reset_progress_confirm") }}')) {
        localStorage.removeItem('videoProgress_{{ $study->id }}');
        location.reload();
    }
}

// Show completion certificate
function showCompletionCertificate() {
    const certificate = document.getElementById('completion-certificate');
    certificate.style.display = 'block';
    certificate.scrollIntoView({ behavior: 'smooth' });
}

// Download certificate
function downloadCertificate() {
    // Create a simple certificate
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 800;
    canvas.height = 600;

    // Draw certificate
    ctx.fillStyle = '#f8fafc';
    ctx.fillRect(0, 0, 800, 600);

    ctx.fillStyle = '#8b5cf6';
    ctx.font = 'bold 48px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('{{ __("messages.certificate") }}', 400, 100);

    ctx.fillStyle = '#1e293b';
    ctx.font = '24px Arial';
    ctx.fillText('{{ __("messages.certificate_text") }}', 400, 200);

    ctx.font = 'bold 32px Arial';
    ctx.fillText('{{ $study->name }}', 400, 300);

    ctx.font = '18px Arial';
    ctx.fillText('{{ __("messages.completed_on") }}: ' + new Date().toLocaleDateString(), 400, 400);

    // Download
    const link = document.createElement('a');
    link.download = '{{ $study->name }} - Certificate.pdf';
    link.href = canvas.toDataURL();
    link.click();
}

// Format time
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

// Handle video events
document.addEventListener('fullscreenchange', function() {
    // Handle fullscreen changes if needed
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === ' ') {
        e.preventDefault();
        // Play/pause current video
        const activeVideo = document.querySelector('video:focus');
        if (activeVideo) {
            if (activeVideo.paused) {
                activeVideo.play();
            } else {
                activeVideo.pause();
            }
        }
    }
});
</script>
@endsection
