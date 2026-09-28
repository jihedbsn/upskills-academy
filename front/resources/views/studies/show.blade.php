<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    @include('layouts.head')
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }

        .modal-content {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-video, .modal-audio {
            max-width: 90%;
            max-height: 90%;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            z-index: 10000;
        }

        .close-modal:hover {
            color: #ddd;
        }

        .play-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: rgba(255,255,255,0.9);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .play-button:hover {
            background: white;
            transform: scale(1.1);
        }

        .play-button i {
            font-size: 30px;
            color: #333;
        }
    </style>
</head>

<body>
    @include('layouts.header')

    <main class="main-content">
        <section class="layout-pt-md layout-pb-lg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Breadcrumb -->
                        <div class="breadcrumbs mb-30">
                            <a href="{{ route('accueil.index') }}" class="text-purple-1">{{ __('messages.home') }}</a>
                            <span class="mx-10">/</span>
                            <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $study->product->id]) }}" class="text-purple-1">{{ $study->product->titre }}</a>
                            <span class="mx-10">/</span>
                            <span class="text-dark-1">{{ $study->name }}</span>
                        </div>

                        <!-- Study Header -->
                        <div class="study-header mb-40">
                            <div class="d-flex items-center justify-between">
                                <div>
                                    <h1 class="text-24 fw-500 text-dark-1 mb-10">{{ $study->name }}</h1>
                                    <p class="text-16 text-light-1">{{ $study->product->titre }}</p>
                                </div>
                                <div class="d-flex items-center">
                                    @if($study->resource && $study->resource->type)
                                        @if($study->resource->type == 'mp4' || $study->resource->type == 'video')
                                            <div class="d-flex items-center mr-20">
                                                <div class="d-flex justify-center items-center size-40 rounded-full bg-purple-3 mr-10">
                                                    <div class="icon-play text-14"></div>
                                                </div>
                                                <span class="text-14 text-light-1">{{ __('messages.video') }}</span>
                                            </div>
                                        @elseif($study->resource->type == 'audio' || $study->resource->type == 'mp3')
                                            <div class="d-flex items-center mr-20">
                                                <div class="d-flex justify-center items-center size-40 rounded-full bg-blue-3 mr-10">
                                                    <div class="icon-music text-14"></div>
                                                </div>
                                                <span class="text-14 text-light-1">{{ __('messages.audio') }}</span>
                                            </div>
                                        @elseif($study->resource->type == 'pdf' || $study->resource->type == 'document' || $study->resource->type == 'book')
                                            <div class="d-flex items-center mr-20">
                                                <div class="d-flex justify-center items-center size-40 rounded-full bg-green-3 mr-10">
                                                    <div class="icon-file-text text-14"></div>
                                                </div>
                                                <span class="text-14 text-light-1">{{ __('messages.document') }}</span>
                                            </div>
                                        @else
                                            <div class="d-flex items-center mr-20">
                                                <div class="d-flex justify-center items-center size-40 rounded-full bg-light-3 mr-10">
                                                    <div class="icon-file text-14"></div>
                                                </div>
                                                <span class="text-14 text-light-1">{{ $study->resource->type }}</span>
                                            </div>
                                        @endif
                                    @endif
                                    @if($study->duration)
                                        <div class="d-flex items-center">
                                            <div class="icon-clock text-14 text-purple-1 mr-5"></div>
                                            <span class="text-14 text-light-1">{{ $study->duration }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Study Content -->
                        <div class="study-content">
                            @if($study->resource && $study->resource->localized_file)
                                @if($study->resource->type == 'mp4' || $study->resource->type == 'video')
                                    <!-- Video Player with Modal -->
                                    <div class="video-player-wrapper mb-30">
                                        <div class="bg-light-4 rounded-8 p-40 text-center">
                                            <div class="play-button mx-auto mb-20" onclick="openVideoModal()">
                                                <i class="icon-play"></i>
                                            </div>
                                            <h3 class="text-18 fw-500 text-dark-1 mb-10">{{ $study->name }}</h3>
                                            <p class="text-14 text-light-1 mb-20">{{ __('messages.click_to_play_video') }}</p>
                                            <button class="button -md -purple-1 text-white" onclick="openVideoModal()">
                                                {{ __('messages.play_video') }}
                                            </button>
                                        </div>
                                    </div>
                                @elseif($study->resource->type == 'audio' || $study->resource->type == 'mp3')
                                    <!-- Audio Player with Modal -->
                                    <div class="audio-player-wrapper mb-30">
                                        <div class="bg-light-4 rounded-8 p-40 text-center">
                                            <div class="play-button mx-auto mb-20" onclick="openAudioModal()">
                                                <i class="icon-music"></i>
                                            </div>
                                            <h3 class="text-18 fw-500 text-dark-1 mb-10">{{ $study->name }}</h3>
                                            <p class="text-14 text-light-1 mb-20">{{ __('messages.click_to_play_audio') }}</p>
                                            <button class="button -md -purple-1 text-white" onclick="openAudioModal()">
                                                {{ __('messages.play_audio') }}
                                            </button>
                                        </div>
                                    </div>
                                @elseif($study->resource->type == 'pdf' || $study->resource->type == 'document' || $study->resource->type == 'book')
                                    <!-- PDF/Book Viewer - Redirect to separate page -->
                                    <div class="pdf-viewer-wrapper mb-30">
                                        <div class="bg-light-4 rounded-8 p-40 text-center">
                                            <div class="d-flex justify-center items-center size-80 rounded-full bg-green-3 mx-auto mb-20">
                                                <div class="icon-file-text text-32"></div>
                                            </div>
                                            <h3 class="text-18 fw-500 text-dark-1 mb-10">{{ $study->name }}</h3>
                                            <p class="text-14 text-light-1 mb-20">{{ __('messages.click_to_view_document') }}</p>
                                            <a href="{{ route('study.pdf', $study) }}"
                                               class="button -md -purple-1 text-white"
                                               target="_blank">
                                                {{ __('messages.view_document') }}
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <!-- Generic File Viewer -->
                                    <div class="file-viewer-wrapper mb-30">
                                        <div class="bg-light-4 rounded-8 p-30 text-center">
                                            <div class="d-flex justify-center items-center size-80 rounded-full bg-light-3 mx-auto mb-20">
                                                <div class="icon-file text-32 text-light-1"></div>
                                            </div>
                                            <h3 class="text-18 fw-500 text-dark-1 mb-10">{{ $study->name }}</h3>
                                            <p class="text-14 text-light-1 mb-20">{{ __('messages.file_type') }}: {{ $study->resource->type }}</p>
                                            <a href="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}"
                                               class="button -md -purple-1 text-white"
                                               target="_blank">
                                                {{ __('messages.open_file') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- No File Available -->
                                <div class="no-file-wrapper mb-30">
                                    <div class="bg-light-4 rounded-8 p-30 text-center">
                                        <div class="d-flex justify-center items-center size-80 rounded-full bg-light-3 mx-auto mb-20">
                                            <div class="icon-alert-circle text-32 text-light-1"></div>
                                        </div>
                                        <h3 class="text-18 fw-500 text-dark-1 mb-10">{{ __('messages.no_file_available') }}</h3>
                                        <p class="text-14 text-light-1">{{ __('messages.file_not_uploaded') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Study Navigation -->
                        <div class="study-navigation mt-40">
                            <div class="d-flex items-center justify-between">
                                <a href="{{ route('courses.show', ['locale' => app()->getLocale(), 'product' => $study->product->id]) }}" class="button -sm -outline-purple-1 text-purple-1">
                                    <div class="icon-arrow-left mr-10"></div>
                                    {{ __('messages.back_to_course') }}
                                </a>
                                <div class="d-flex items-center">
                                    @if($study->resource && $study->resource->localized_file)
                                        <a href="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}"
                                           class="button -sm -purple-1 text-white ml-10"
                                           target="_blank">
                                            <div class="icon-download mr-10"></div>
                                            {{ __('messages.download') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Video Modal -->
    @if($study->resource && $study->resource->localized_file && ($study->resource->type == 'mp4' || $study->resource->type == 'video'))
    <div id="videoModal" class="modal">
        <span class="close-modal" onclick="closeVideoModal()">&times;</span>
        <div class="modal-content">
            <video id="modalVideo" controls class="modal-video">
                <source src="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}" type="video/mp4">
                {{ __('messages.video_not_supported') }}
            </video>
        </div>
    </div>
    @endif

    <!-- Audio Modal -->
    @if($study->resource && $study->resource->localized_file && ($study->resource->type == 'audio' || $study->resource->type == 'mp3'))
    <div id="audioModal" class="modal">
        <span class="close-modal" onclick="closeAudioModal()">&times;</span>
        <div class="modal-content">
            <div class="bg-white rounded-8 p-40 text-center" style="max-width: 500px;">
                <div class="d-flex justify-center items-center size-80 rounded-full bg-purple-1 mx-auto mb-30">
                    <div class="icon-music text-32 text-white"></div>
                </div>
                <h3 class="text-24 fw-500 text-dark-1 mb-15">{{ $study->name }}</h3>
                <p class="text-16 text-light-1 mb-30">{{ $study->product->titre }}</p>
                <audio id="modalAudio" controls class="w-1/1">
                    <source src="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}" type="audio/mpeg">
                    {{ __('messages.audio_not_supported') }}
                </audio>
            </div>
        </div>
    </div>
    @endif

    @include('layouts.footer')

    <script>
        // Video Modal Functions
        function openVideoModal() {
            document.getElementById('videoModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeVideoModal() {
            document.getElementById('videoModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('modalVideo').pause();
        }

        // Audio Modal Functions
        function openAudioModal() {
            document.getElementById('audioModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeAudioModal() {
            document.getElementById('audioModal').style.display = 'none';
            document.body.style.overflow = 'auto';
            document.getElementById('modalAudio').pause();
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const videoModal = document.getElementById('videoModal');
            const audioModal = document.getElementById('audioModal');

            if (event.target == videoModal) {
                closeVideoModal();
            }
            if (event.target == audioModal) {
                closeAudioModal();
            }
        }

        // Close modals with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeVideoModal();
                closeAudioModal();
            }
        });
    </script>
</body>
</html>
