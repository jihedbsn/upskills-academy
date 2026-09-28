<div class="pt-60 lg:pt-40 to-over">
  <h4 class="text-18 fw-500">{{ __('messages.course_of_study') }}</h4>

  @php
    // Vérifier si l'étudiant a acheté le cours
    $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());

    // Check installment-based content access
    $hasInstallmentOrder = isset($installmentContentInfo) && $installmentContentInfo && ($installmentContentInfo['has_installment_order'] ?? false);
    $unlockedStudyIds = isset($installmentContentInfo['unlocked_study_ids'])
        ? ($installmentContentInfo['unlocked_study_ids'])
        : [];
    $isSuspended = $hasInstallmentOrder ? ($installmentContentInfo['is_suspended'] ?? false) : false;
    $isAllUnlocked = $hasInstallmentOrder ? ($installmentContentInfo['is_all_unlocked'] ?? false) : true;
    $unlockedMonths = $hasInstallmentOrder ? ($installmentContentInfo['unlocked_months'] ?? 0) : 0;
    $totalMonths = $hasInstallmentOrder ? ($installmentContentInfo['total_months'] ?? 0) : 0;
  @endphp

  @if($product->studies && $product->studies->count() > 0)
    @if($hasPurchased && $hasInstallmentOrder && !$isAllUnlocked)
      {{-- Installment Content Progress --}}
      <div class="mt-20 mb-10 p-20 rounded-8 border-light bg-light-4">
        <div class="d-flex items-center justify-between mb-10">
          <span class="text-14 fw-500 text-dark-1">
            <i class="fas fa-lock-open text-green-1 me-2"></i>
            {{ __('messages.content_unlocked') }}: {{ $unlockedMonths }} / {{ $totalMonths }} {{ __('messages.months') }}
          </span>
          @if($isSuspended)
            <span class="badge bg-danger text-white px-10 py-5 rounded-4">
              <i class="fas fa-exclamation-triangle me-1"></i> {{ __('messages.suspended') }}
            </span>
          @endif
        </div>
        <div class="progress" style="height: 6px; border-radius: 3px;">
          <div class="progress-bar bg-green-1" role="progressbar" style="width: {{ $totalMonths > 0 ? ($unlockedMonths / $totalMonths) * 100 : 0 }}%; border-radius: 3px;"></div>
        </div>
        @if($isSuspended)
          <p class="text-12 text-red-1 mt-10 mb-0">
            <i class="fas fa-info-circle me-1"></i>
            {{ __('messages.pay_overdue_installment') }}
          </p>
        @endif
      </div>
    @endif

    <div class="d-flex justify-between items-center mt-30">
      <div class="">{{ $product->studies->count() }} {{ __('messages.sections') }} • {{ $product->studies->count() }} {{ __('messages.lectures') }}</div>
      <a href="#" class="underline text-purple-1">{{ __('messages.expand_all_sections') }}</a>
    </div>

    <div class="mt-10">
      <div class="accordion -block-2 text-left js-accordion">
        @foreach($product->studies as $index => $study)
          @php
            $hasScheduleInfo = $hasPurchased && auth()->check() && isset($installmentContentInfo['unlocked_study_ids']);
            $isStudyLockedForStudent = $hasPurchased && (
                ($hasScheduleInfo && $isSuspended)
                || ($hasScheduleInfo && !in_array((int) $study->id, array_map('intval', $unlockedStudyIds), true))
            );
          @endphp
          <div class="accordion__item {{ $isStudyLockedForStudent ? 'opacity-75' : '' }}">
            <div class="accordion__button py-20 px-30 {{ $isStudyLockedForStudent ? 'bg-light-6' : 'bg-light-4' }}">
              <div class="d-flex items-center">
                <div class="accordion__icon">
                  @if($isStudyLockedForStudent)
                    <div class="icon text-light-1"><i class="fas fa-lock" style="font-size: 14px;"></i></div>
                    <div class="icon text-light-1"><i class="fas fa-lock" style="font-size: 14px;"></i></div>
                  @else
                    <div class="icon" data-feather="chevron-down"></div>
                    <div class="icon" data-feather="chevron-up"></div>
                  @endif
                </div>
                <span class="text-17 fw-500 {{ $isStudyLockedForStudent ? 'text-light-1' : 'text-dark-1' }}">{{ $study->name }}</span>
              </div>
              <div>
                @if($isStudyLockedForStudent)
                  <span class="text-12 text-light-1"><i class="fas fa-lock me-1"></i>{{ __('messages.locked') ?? 'Locked' }}</span>
                @else
                  1 {{ __('messages.lecture') }}
                @endif
              </div>
            </div>

            <div class="accordion__content">
              <div class="accordion__content__inner px-30 py-30">
                <div class="y-gap-20">
                  @if($isStudyLockedForStudent)
                    {{-- Locked content message --}}
                    <div class="d-flex items-center justify-between">
                      <div class="d-flex items-center">
                        <svg class="lesson-status-icon lesson-status-icon--left" width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" focusable="false">
                          <circle cx="7" cy="7" r="5.5" fill="none" stroke="#d1d5db" stroke-width="2" />
                        </svg>
                        <div class="text-light-1">{{ $study->name }}</div>
                      </div>
                      <div>
                        @if($hasScheduleInfo && $isSuspended)
                          <span class="text-13 text-red-1 fw-500"><i class="fas fa-exclamation-circle me-1"></i>{{ __('messages.suspended') }}</span>
                        @else
                          <span class="text-13 text-warning fw-500"><i class="fas fa-lock me-1"></i>{{ __('messages.locked') ?? 'Locked' }}</span>
                        @endif
                      </div>
                    </div>
                  @else
                    <div class="d-flex justify-between">
                    @php
                      // Determine availability of this study's resource (for status icon)
                      $isAvailable = false;
                      $iconClass = 'fa-file';

                      if ($study->resource) {
                        $type = $study->resource->type;
                        // Determine type icon
                        if ($type == 'mp4' || $type == 'video') {
                            $iconClass = 'fa-play-circle';
                        } elseif ($type == 'audio' || $type == 'mp3') {
                            $iconClass = 'fa-music';
                        } elseif ($type == 'pdf' || $type == 'document' || $type == 'book') {
                            $iconClass = 'fa-file-pdf';
                        }

                        // Determine availability
                        if (($type == 'mp4' || $type == 'video') && ($study->resource->localized_file || $study->resource->video_files || $study->resource->video_files_multilingual)) {
                          $isAvailable = true;
                        } elseif (($type == 'audio' || $type == 'mp3') && $study->resource->localized_file) {
                          $isAvailable = true;
                        } elseif (($type == 'pdf' || $type == 'document' || $type == 'book') && $study->resource->localized_file) {
                          $isAvailable = true;
                        } elseif ($study->resource->localized_file) {
                          $isAvailable = true;
                        }
                      }
                    @endphp

                    <div class="d-flex items-center">
                      <div class="d-flex justify-center items-center me-3" style="min-width: 20px;">
                        <i class="fas {{ $iconClass }} {{ $isAvailable ? 'text-blue-1' : 'text-light-1' }}" style="font-size: 16px; {{ $isAvailable ? 'color: #0071ae;' : '' }}"></i>
                      </div>
                      <div>{{ $study->name }}</div>
                    </div>

                    <div class="d-flex x-gap-20 items-center">
                      @if($hasPurchased)
                        {{-- Content accessible for enrolled students --}}
                        @if($isAvailable)
                          @if(($study->resource->type == 'mp4' || $study->resource->type == 'video'))
                            <a href="{{ route('study.videos', $study) }}" target="_blank" class="text-14 lh-1 text-purple-1 underline">
                              @php
                                $videoCount = count($study->resource->getAllLocalizedVideos());
                              @endphp
                              @if($videoCount > 1)
                                {{ __('messages.preview') }} ({{ $videoCount }} {{ __('messages.videos') }})
                              @else
                                {{ __('messages.preview') }}
                              @endif
                            </a>
                          @elseif(($study->resource->type == 'audio' || $study->resource->type == 'mp3'))
                            <button onclick="openAudioModal('{{ $study->id }}', '{{ $study->name }}', '{{ $product->titre }}', '{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}')"
                                    class="text-14 lh-1 text-purple-1 underline bg-none border-none cursor-pointer">{{ __('messages.listen') }}</button>
                          @elseif($study->resource->type == 'pdf' || $study->resource->type == 'document' || $study->resource->type == 'book')
                            <a href="{{ route('study.pdf', $study) }}" target="_blank" class="text-14 lh-1 text-purple-1 underline">{{ __('messages.view') }}</a>
                          @else
                            <a href="{{ config('app.file_url') }}uploads/resources/{{ $study->resource->localized_file }}" target="_blank" class="text-14 lh-1 text-purple-1 underline">{{ __('messages.open') }}</a>
                          @endif
                        @else
                          <span class="text-14 lh-1 text-light-1">{{ __('messages.unavailable') ?? 'Unavailable' }}</span>
                        @endif
                      @else
                        {{-- Message for non-enrolled users --}}
                        @if(auth()->check())
                          <span class="text-14 lh-1 text-red-1 fw-500">{{ __('messages.enroll_to_access') }}</span>
                        @else
                          <a href="{{ route('login') }}" class="text-14 lh-1 text-purple-1 underline">{{ __('messages.login_to_preview') }}</a>
                        @endif
                      @endif
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @else
    <div class="mt-30">
      <p class="text-14 text-light-1">{{ __('messages.no_studies_available') }}</p>
    </div>
  @endif
</div>

<!-- Video Modal -->
<div id="videoModal" class="modal">
  <span class="close-modal" onclick="closeVideoModal()">&times;</span>
  <div class="modal-content">
    <video id="modalVideo" controls class="modal-video">
      <source id="videoSource" src="" type="video/mp4">
      {{ __('messages.video_not_supported') }}
    </video>
  </div>
</div>

<!-- Audio Modal -->
<div id="audioModal" class="modal">
  <span class="close-modal" onclick="closeAudioModal()">&times;</span>
  <div class="modal-content">
    <div class="bg-white rounded-8 p-40" style="max-width: 800px; width: 90%;">
      <div class="text-center mb-40">
        <div class="d-flex justify-center items-center size-80 rounded-full bg-blue-1 mx-auto mb-30">
          <div class="icon-music text-32 text-white"></div>
        </div>
        <h3 id="audioTitle" class="text-24 fw-500 text-dark-1 mb-15"></h3>
        <p id="audioCourse" class="text-16 text-light-1 mb-0"></p>
      </div>

      <!-- Hidden native audio element -->
      <audio id="modalAudio" style="display: none;">
        <source id="audioSource" src="" type="audio/mpeg">
      </audio>

      <!-- Custom Audio Player Controls -->
      <div class="custom-audio-player">
        <!-- Progress Bar -->
        <div class="audio-progress-container mb-20">
          <input type="range" id="audioProgress" class="audio-progress-bar" min="0" max="100" value="0" step="0.1">
          <div class="audio-time-display mt-10">
            <span id="audioCurrentTime">0:00</span>
            <span id="audioDuration">0:00</span>
          </div>
        </div>

        <!-- Main Controls -->
        <div class="audio-main-controls mb-20">
          <button id="skipBackBtn" class="audio-control-btn" title="Skip back 10s">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M11.99 5V1l-5 5 5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6h-2c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
              <text x="9" y="16" font-size="8" font-weight="bold" fill="currentColor">10</text>
            </svg>
          </button>

          <button id="playPauseBtn" class="audio-control-btn audio-control-btn-large">
            <svg id="playIcon" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
              <path d="M8 5v14l11-7z"/>
            </svg>
            <svg id="pauseIcon" width="32" height="32" viewBox="0 0 24 24" fill="currentColor" style="display: none;">
              <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
            </svg>
          </button>

          <button id="skipForwardBtn" class="audio-control-btn" title="Skip forward 10s">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z"/>
              <text x="9" y="16" font-size="8" font-weight="bold" fill="currentColor">10</text>
            </svg>
          </button>
        </div>

        <!-- Additional Controls -->
        <div class="audio-additional-controls">
          <div class="audio-control-group">
            <label class="audio-control-label">Speed</label>
            <select id="playbackSpeed" class="audio-speed-select">
              <option value="0.5">0.5x</option>
              <option value="0.75">0.75x</option>
              <option value="1" selected>1x</option>
              <option value="1.25">1.25x</option>
              <option value="1.5">1.5x</option>
              <option value="1.75">1.75x</option>
              <option value="2">2x</option>
            </select>
          </div>

          <div class="audio-control-group">
            <label class="audio-control-label">Volume</label>
            <input type="range" id="volumeControl" class="audio-volume-slider" min="0" max="100" value="100">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


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

  .bg-none {
    background: none;
  }

  .border-none {
    border: none;
  }

  .cursor-pointer {
    cursor: pointer;
  }

  /* Custom Audio Player Styles */
  .custom-audio-player {
    background: #f8f9fa;
    padding: 30px;
    border-radius: 12px;
  }

  .audio-progress-container {
    margin-bottom: 20px;
  }

  .audio-progress-bar {
    width: 100%;
    height: 8px;
    -webkit-appearance: none;
    appearance: none;
    background: #ddd;
    border-radius: 4px;
    outline: none;
    cursor: pointer;
  }

  .audio-progress-bar::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 18px;
    height: 18px;
    background: #65C2F9;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
  }

  .audio-progress-bar::-moz-range-thumb {
    width: 18px;
    height: 18px;
    background: #65C2F9;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    border: none;
  }

  .audio-time-display {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    color: #666;
    margin-top: 8px;
  }

  .audio-main-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
  }

  .audio-control-btn {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    color: #333;
  }

  .audio-control-btn:hover {
    background: #65C2F9;
    border-color: #65C2F9;
    color: white;
    transform: scale(1.05);
  }

  .audio-control-btn-large {
    width: 70px;
    height: 70px;
    background: #65C2F9;
    border-color: #65C2F9;
    color: white;
  }

  .audio-control-btn-large:hover {
    background: #4FB5F0;
    border-color: #4FB5F0;
    transform: scale(1.1);
  }

  .audio-additional-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 30px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
  }

  .audio-control-group {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .audio-control-label {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin-bottom: 0;
  }

  .audio-speed-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: white;
    font-size: 14px;
    cursor: pointer;
    outline: none;
  }

  .audio-speed-select:focus {
    border-color: #65C2F9;
  }

  .audio-volume-slider {
    width: 120px;
    height: 6px;
    -webkit-appearance: none;
    appearance: none;
    background: #ddd;
    border-radius: 3px;
    outline: none;
    cursor: pointer;
  }

  .audio-volume-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 14px;
    height: 14px;
    background: #65C2F9;
    border-radius: 50%;
    cursor: pointer;
  }

  .audio-volume-slider::-moz-range-thumb {
    width: 14px;
    height: 14px;
    background: #65C2F9;
    border-radius: 50%;
    cursor: pointer;
    border: none;
  }

  @media (max-width: 768px) {
    .custom-audio-player {
      padding: 20px;
    }

    .audio-additional-controls {
      flex-direction: column;
      gap: 15px;
    }

    .audio-control-group {
      width: 100%;
      justify-content: space-between;
    }

    .audio-volume-slider {
      width: 150px;
    }
  }

</style>

<style>
  /* Lesson status icon spacing */
  .lesson-status-icon { display:inline-block; vertical-align:middle; }
  [dir="rtl"] .lesson-status-icon { margin-left: 6px; }
  [dir="ltr"] .lesson-status-icon { margin-right: 6px; }
  /* Slightly align the icon visually with text */
  .lesson-status-icon--left { transform: translateY(-1px); }
</style>

<script>
  // Video Modal Functions
  function openVideoModal(studyId, studyName, videoUrl) {
    document.getElementById('videoSource').src = videoUrl;
    document.getElementById('modalVideo').load();
    document.getElementById('videoModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
  }

  function closeVideoModal() {
    document.getElementById('videoModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('modalVideo').pause();
  }

  // Audio Modal Functions
  function openAudioModal(studyId, studyName, courseName, audioUrl) {
    document.getElementById('audioTitle').textContent = studyName;
    document.getElementById('audioCourse').textContent = courseName;
    document.getElementById('audioSource').src = audioUrl;
    document.getElementById('modalAudio').load();
    document.getElementById('audioModal').style.display = 'block';
    document.body.style.overflow = 'hidden';

    // Initialize audio controls
    initAudioControls();
  }

  function closeAudioModal() {
    document.getElementById('audioModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    const audio = document.getElementById('modalAudio');
    audio.pause();
    audio.currentTime = 0;

    // Reset UI
    document.getElementById('playIcon').style.display = 'block';
    document.getElementById('pauseIcon').style.display = 'none';
  }

  // Custom Audio Player Controls
  function initAudioControls() {
    const audio = document.getElementById('modalAudio');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const playIcon = document.getElementById('playIcon');
    const pauseIcon = document.getElementById('pauseIcon');
    const skipBackBtn = document.getElementById('skipBackBtn');
    const skipForwardBtn = document.getElementById('skipForwardBtn');
    const progressBar = document.getElementById('audioProgress');
    const currentTimeDisplay = document.getElementById('audioCurrentTime');
    const durationDisplay = document.getElementById('audioDuration');
    const speedSelect = document.getElementById('playbackSpeed');
    const volumeControl = document.getElementById('volumeControl');

    // Format time helper
    function formatTime(seconds) {
      const mins = Math.floor(seconds / 60);
      const secs = Math.floor(seconds % 60);
      return `${mins}:${secs.toString().padStart(2, '0')}`;
    }

    // Play/Pause
    playPauseBtn.onclick = function() {
      if (audio.paused) {
        audio.play();
        playIcon.style.display = 'none';
        pauseIcon.style.display = 'block';
      } else {
        audio.pause();
        playIcon.style.display = 'block';
        pauseIcon.style.display = 'none';
      }
    };

    // Skip back 10 seconds
    skipBackBtn.onclick = function() {
      audio.currentTime = Math.max(0, audio.currentTime - 10);
    };

    // Skip forward 10 seconds
    skipForwardBtn.onclick = function() {
      audio.currentTime = Math.min(audio.duration, audio.currentTime + 10);
    };

    // Update progress bar and time
    audio.ontimeupdate = function() {
      if (audio.duration) {
        const progress = (audio.currentTime / audio.duration) * 100;
        progressBar.value = progress;
        currentTimeDisplay.textContent = formatTime(audio.currentTime);
      }
    };

    // Update duration when loaded
    audio.onloadedmetadata = function() {
      durationDisplay.textContent = formatTime(audio.duration);
      progressBar.max = 100;
    };

    // Seek when progress bar is changed
    progressBar.oninput = function() {
      const time = (progressBar.value / 100) * audio.duration;
      audio.currentTime = time;
    };

    // Playback speed
    speedSelect.onchange = function() {
      audio.playbackRate = parseFloat(speedSelect.value);
    };

    // Volume control
    volumeControl.oninput = function() {
      audio.volume = volumeControl.value / 100;
    };

    // Keyboard shortcuts
    document.addEventListener('keydown', function audioKeyHandler(e) {
      if (document.getElementById('audioModal').style.display === 'block') {
        switch(e.key) {
          case ' ':
            e.preventDefault();
            playPauseBtn.click();
            break;
          case 'ArrowLeft':
            e.preventDefault();
            skipBackBtn.click();
            break;
          case 'ArrowRight':
            e.preventDefault();
            skipForwardBtn.click();
            break;
        }
      }
    });

    // Auto-hide play icon and show pause when audio ends
    audio.onended = function() {
      playIcon.style.display = 'block';
      pauseIcon.style.display = 'none';
      audio.currentTime = 0;
    };
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
