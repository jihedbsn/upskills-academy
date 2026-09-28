@php
  $hasPurchased = auth()->check() && $product->hasStudentAccess(auth()->id());

  // Check stage completion
  $stageCompleted = false;
  if ($hasPurchased && $product->is_stage) {
      $stageSubmission = \App\Models\StudentStageCourse::where('student_id', auth()->id())
          ->where('product_id', $product->id)
          ->where('is_valid', 1)
          ->exists();
      $stageCompleted = $stageSubmission;
  }

  $currentLang = app()->getLocale();
  $stageVariation = $product->variations()->where('langue', $currentLang)->first();
  $canSubmitStage = $hasPurchased ? $product->canStudentSubmitStage(auth()->id()) : false;
  $existingSubmission = $hasPurchased ? \App\Models\StudentStageCourse::where('student_id', auth()->id())
      ->where('product_id', $product->id)
      ->first() : null;
@endphp

<div class="pt-60 lg:pt-40">
  <!-- Section Stage Course -->
  <div class="exam-section mb-50">
    <div class="section-header mb-30">
      <div class="d-flex items-center">
        <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-green-400 to-green-500 mr-20 shadow-sm">
          <div class="icon-briefcase text-16 text-white"></div>
        </div>
        <div>
          <div class="d-flex items-center">
            <h4 class="text-20 fw-600 text-dark-1">{{ __('messages.stage_course') }}</h4>
            @if($stageCompleted)
              <div class="section-success-badge ml-15">
                <div class="icon-check-circle text-14 text-green-1"></div>
                <span class="text-12 fw-500 text-green-1">{{ __('messages.completed') }}</span>
              </div>
            @endif
          </div>
          <div class="section-stats mt-10">
            <span class="stat-item">{{ __('messages.practical_work') }}</span>
            <span class="stat-separator">•</span>
            <span class="stat-item">{{ __('messages.certification_required') }}</span>
          </div>
        </div>
      </div>
    </div>

    <div class="section-content-stage">
      {{-- Stage Description --}}
      @if($stageVariation && $stageVariation->description_stage)
        <div class="description-card mb-30">
          <div class="d-flex items-center mb-15">
            <div class="d-flex justify-center items-center size-36 rounded-full bg-gradient-to-r from-green-400 to-green-500 mr-15 shadow-sm">
              <div class="icon-info text-12 text-white"></div>
            </div>
            <h5 class="text-16 fw-600 text-dark-1">{{ __('messages.stage_description') }}</h5>
          </div>
          <div class="text-14 text-dark-1 leading-relaxed pl-51">
            {!! $stageVariation->description_stage !!}
          </div>
        </div>
      @endif

      @if($hasPurchased)
        {{-- Submission Status Display --}}
        <div id="stageSubmissionStatus">
          @if($existingSubmission)
            <div class="stage-status-card mb-30 {{ $existingSubmission->is_valid == 1 ? 'status-validated' : ($existingSubmission->is_valid == -1 ? 'status-rejected' : 'status-pending') }}">
              <div class="status-icon">
                @if($existingSubmission->is_valid == 1)
                  <i class="icon-check-circle"></i>
                @elseif($existingSubmission->is_valid == -1)
                  <i class="icon-x-circle"></i>
                @else
                  <i class="icon-clock"></i>
                @endif
              </div>
              <div class="status-content">
                <h5 class="status-title">
                  @if($existingSubmission->is_valid == 1)
                    {{ __('messages.submission_validated') }}
                  @elseif($existingSubmission->is_valid == -1)
                    {{ __('messages.submission_rejected') }}
                  @else
                    {{ __('messages.submission_pending') }}
                  @endif
                </h5>
                <p class="status-date">{{ __('messages.submitted_on') }}: {{ $existingSubmission->submitted_at->format('d/m/Y H:i') }}</p>
                @if($existingSubmission->admin_notes)
                  <p class="status-notes"><strong>{{ __('messages.admin_notes') }}:</strong> {{ $existingSubmission->admin_notes }}</p>
                @endif
              </div>
            </div>
          @endif
        </div>

        {{-- Submission Form - Show if no submission or if rejected --}}
        @if(!$existingSubmission || $existingSubmission->is_valid == -1)
          @if($canSubmitStage)
            <div class="stage-submission-card" id="stageSubmissionCard">
              <div class="submission-header">
                <div class="d-flex items-center mb-20">
                  <div class="d-flex justify-center items-center size-48 rounded-full bg-gradient-to-r from-green-400 to-green-500 mr-20 shadow-sm">
                    <div class="icon-upload text-16 text-white"></div>
                  </div>
                  <div>
                    <h5 class="text-18 fw-600 text-dark-1">{{ __('messages.submit_stage_work') }}</h5>
                  </div>
                </div>
              </div>

              <form id="stageSubmissionForm" class="stage-form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                <div class="form-grid">
                  {{-- File 1 - Required --}}
                  <div class="form-group">
                    <label class="form-label">
                      <div class="label-icon">
                        <i class="icon-file text-14"></i>
                      </div>
                      <span>{{ __('messages.file_1') }} <span class="text-danger">*</span></span>
                    </label>
                    <div class="stage-file-upload-zone" id="uploadZone1" data-input="stageFile1">
                      <input type="file"
                             id="stageFile1"
                             name="file1"
                             class="stage-file-input"
                             accept=".jpg,.jpeg,.png,.gif,.pdf"
                             required>
                      <div class="upload-placeholder" id="placeholder1">
                        <div class="upload-icon-wrapper">
                          <i class="icon-upload-cloud"></i>
                        </div>
                        <div class="upload-text-wrapper">
                          <span class="upload-main-text">{{ __('messages.click_to_upload') }}</span>
                          <span class="upload-sub-text">JPG, PNG, GIF, PDF ({{ __('messages.max_size') }}: 10MB)</span>
                        </div>
                      </div>
                      <div class="file-selected-preview" id="preview1" style="display: none;">
                        <div class="preview-icon">
                          <i class="icon-file-text"></i>
                        </div>
                        <div class="preview-info">
                          <span class="preview-name" id="fileName1"></span>
                          <span class="preview-size" id="fileSize1"></span>
                        </div>
                        <button type="button" class="preview-remove" onclick="removeStageFile(1)">
                          <i class="icon-x"></i>
                        </button>
                      </div>
                    </div>
                  </div>

                  {{-- File 2 - Optional --}}
                  <div class="form-group">
                    <label class="form-label">
                      <div class="label-icon">
                        <i class="icon-file text-14"></i>
                      </div>
                      <span>{{ __('messages.file_2') }}</span>
                      <span class="optional-badge">{{ __('messages.optional') }}</span>
                    </label>
                    <div class="stage-file-upload-zone" id="uploadZone2" data-input="stageFile2">
                      <input type="file"
                             id="stageFile2"
                             name="file2"
                             class="stage-file-input"
                             accept=".jpg,.jpeg,.png,.gif,.pdf">
                      <div class="upload-placeholder" id="placeholder2">
                        <div class="upload-icon-wrapper">
                          <i class="icon-upload-cloud"></i>
                        </div>
                        <div class="upload-text-wrapper">
                          <span class="upload-main-text">{{ __('messages.click_to_upload') }}</span>
                          <span class="upload-sub-text">JPG, PNG, GIF, PDF ({{ __('messages.max_size') }}: 10MB)</span>
                        </div>
                      </div>
                      <div class="file-selected-preview" id="preview2" style="display: none;">
                        <div class="preview-icon">
                          <i class="icon-file-text"></i>
                        </div>
                        <div class="preview-info">
                          <span class="preview-name" id="fileName2"></span>
                          <span class="preview-size" id="fileSize2"></span>
                        </div>
                        <button type="button" class="preview-remove" onclick="removeStageFile(2)">
                          <i class="icon-x"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Description - Optional --}}
                <div class="form-group mt-20">
                  <label class="form-label">
                    <div class="label-icon">
                      <i class="icon-edit text-14"></i>
                    </div>
                    <span>{{ __('messages.describe_your_stage_work') }}</span>
                    <span class="optional-badge">{{ __('messages.optional') }}</span>
                  </label>
                  <textarea name="description"
                            id="stageDescription"
                            rows="4"
                            class="form-textarea"
                            placeholder="{{ __('messages.describe_your_stage_work_placeholder') }}"></textarea>
                </div>

                {{-- Submit Button --}}
                <div class="form-actions mt-25">
                  <button type="submit" class="submit-btn" id="submitStageBtn">
                    <i class="icon-upload mr-10"></i>
                    <span>{{ __('messages.submit_stage_work') }}</span>
                  </button>
                </div>
              </form>
            </div>
          @else
            <div class="stage-requirements-card">
              <div class="requirements-icon">
                <i class="icon-alert-circle"></i>
              </div>
              <h5>{{ __('messages.cannot_submit_stage') }}</h5>
              <p>{{ __('messages.stage_submission_not_available') }}</p>
            </div>
          @endif
        @endif
      @else
        {{-- Locked State --}}
        <div class="locked-stage-card">
          <div class="locked-content">
            <div class="locked-icon">
              <i class="icon-lock text-32 text-light-1"></i>
            </div>
            <h5 class="text-18 fw-600 text-dark-1 mb-10">{{ __('messages.stage_course_locked') }}</h5>
            <p class="text-14 text-light-1 mb-20">{{ __('messages.purchase_course_to_access_stage') }}</p>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  // ========== STAGE SUBMISSION COMPONENT ==========
  function initStageUpload() {
    const zones = document.querySelectorAll('.stage-file-upload-zone');

    zones.forEach(zone => {
      const inputId = zone.dataset.input;
      const input = document.getElementById(inputId);
      if (!input) return;

      const zoneNumber = inputId.replace('stageFile', '');
      const placeholder = document.getElementById('placeholder' + zoneNumber);
      const preview = document.getElementById('preview' + zoneNumber);
      const fileNameEl = document.getElementById('fileName' + zoneNumber);
      const fileSizeEl = document.getElementById('fileSize' + zoneNumber);

      // Click to upload
      zone.addEventListener('click', function(e) {
        if (e.target.closest('.preview-remove')) {
          return;
        }
        input.click();
      });

      // File selection handler
      input.addEventListener('change', function() {
        handleStageFileSelection(input, zone, placeholder, preview, fileNameEl, fileSizeEl);
      });

      // Drag and drop
      zone.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.add('dragover');
      });

      zone.addEventListener('dragenter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.add('dragover');
      });

      zone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove('dragover');
      });

      zone.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        zone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
          const dt = new DataTransfer();
          dt.items.add(files[0]);
          input.files = dt.files;
          handleStageFileSelection(input, zone, placeholder, preview, fileNameEl, fileSizeEl);
        }
      });
    });
  }

  function handleStageFileSelection(input, zone, placeholder, preview, fileNameEl, fileSizeEl) {
    const file = input.files[0];

    if (!file) {
      zone.classList.remove('has-file');
      placeholder.style.display = 'flex';
      preview.style.display = 'none';
      return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
      input.value = '';
      Swal.fire({
        icon: 'error',
        title: '{{ __("messages.invalid_file_type") }}',
        text: '{{ __("messages.only_images_and_pdf_allowed") }}',
        confirmButtonColor: '#10b981'
      });
      return;
    }

    // Validate file size (10MB max)
    const maxSize = 10 * 1024 * 1024;
    if (file.size > maxSize) {
      input.value = '';
      Swal.fire({
        icon: 'error',
        title: '{{ __("messages.file_too_large") }}',
        text: '{{ __("messages.file_size_limit_10mb") }}',
        confirmButtonColor: '#10b981'
      });
      return;
    }

    // Show file preview
    zone.classList.add('has-file');
    placeholder.style.display = 'none';
    preview.style.display = 'flex';

    // Truncate filename if too long
    const maxNameLength = 25;
    let displayName = file.name;
    if (displayName.length > maxNameLength) {
      const ext = displayName.split('.').pop();
      displayName = displayName.substring(0, maxNameLength - ext.length - 4) + '...' + ext;
    }

    fileNameEl.textContent = displayName;
    fileSizeEl.textContent = formatFileSize(file.size);
  }

  function removeStageFile(number) {
    const input = document.getElementById('stageFile' + number);
    const zone = document.getElementById('uploadZone' + number);
    const placeholder = document.getElementById('placeholder' + number);
    const preview = document.getElementById('preview' + number);

    if (input) input.value = '';
    if (zone) zone.classList.remove('has-file');
    if (placeholder) placeholder.style.display = 'flex';
    if (preview) preview.style.display = 'none';
  }

  function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  // Stage form submission
  function initStageForm() {
    const form = document.getElementById('stageSubmissionForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const file1Input = document.getElementById('stageFile1');
      const file1 = file1Input ? file1Input.files[0] : null;

      // Validate required file
      if (!file1) {
        Swal.fire({
          icon: 'warning',
          title: '{{ __("messages.validation_error") }}',
          text: '{{ __("messages.please_select_file_1") }}',
          confirmButtonColor: '#10b981'
        });
        return;
      }

      // Confirm submission
      Swal.fire({
        title: '{{ __("messages.confirm_submission") }}',
        text: '{{ __("messages.are_you_sure_submit_stage_work") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("messages.yes_submit") }}',
        cancelButtonText: '{{ __("messages.cancel") }}'
      }).then((result) => {
        if (result.isConfirmed) {
          submitStageForm();
        }
      });
    });
  }

  function submitStageForm() {
    const form = document.getElementById('stageSubmissionForm');
    const submitBtn = document.getElementById('submitStageBtn');

    if (!form || !submitBtn) return;

    // Create FormData
    const formData = new FormData(form);

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="icon-loader animate-spin mr-10"></i>{{ __("messages.submitting") }}...';

    Swal.fire({
      title: '{{ __("messages.submitting_stage_work") }}',
      text: '{{ __("messages.please_wait") }}',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    const token = csrfToken ? csrfToken.getAttribute('content') : '';

    const locale = '{{ app()->getLocale() }}';
    fetch(`/${locale}/student-stage-courses`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      }
    })
    .then(response => {
      if (!response.ok) {
        return response.json().then(err => Promise.reject(err));
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: '{{ __("messages.success") }}',
          text: data.message || '{{ __("messages.stage_course_submitted_successfully") }}',
          confirmButtonColor: '#10b981'
        }).then(() => {
          // Reload the page to show updated status
          window.location.reload();
        });
      } else {
        throw new Error(data.message || '{{ __("messages.error_submitting_stage_course") }}');
      }
    })
    .catch(error => {
      console.error('Stage submission error:', error);

      let errorMessage = '{{ __("messages.unexpected_error_occurred") }}';
      if (error.message) {
        errorMessage = error.message;
      } else if (error.errors) {
        errorMessage = Object.values(error.errors).flat().join('\n');
      }

      Swal.fire({
        icon: 'error',
        title: '{{ __("messages.error") }}',
        text: errorMessage,
        confirmButtonColor: '#ef4444'
      });
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.innerHTML = '<i class="icon-upload mr-10"></i>{{ __("messages.submit_stage_work") }}';
    });
  }

  // Initialize stage component on page load
  document.addEventListener('DOMContentLoaded', function() {
    initStageUpload();
    initStageForm();
  });
  // ========== END STAGE SUBMISSION COMPONENT ==========
</script>
