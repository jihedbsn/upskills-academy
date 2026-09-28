@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.create_new_course') }}</h4>
            <a href="{{ route('admin.products.index') }}" class="button -md -light-1">
              <i class="icon-arrow-left mr-10"></i>
              {{ __('messages.back_to_courses') }}
            </a>
          </div>

          <form id="createCourseForm" class="contact-form row y-gap-30" enctype="multipart/form-data">
            @csrf
            
            <!-- Basic Information Section -->
            <div class="col-12">
              <div class="rounded-16 bg-light-4 p-30">
                <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.basic_information') }}</h5>
                
                <div class="row y-gap-30">
                  <!-- Course Image -->
                  <div class="col-12">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.course_image') }} *</label>
                    <input type="file" name="image" id="courseImage" accept="image/*" class="form-control" required>
                    <div class="text-14 text-light-1 mt-5">{{ __('messages.image_requirements') }}</div>
                    <div id="image-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Course Title (English) -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.course_title_en') }} *</label>
                    <input type="text" name="titre_en" id="titreEn" placeholder="{{ __('messages.enter_course_title_en') }}" class="form-control" required>
                    <div id="titre_en-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Course Title (Arabic) -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.course_title_ar') }} *</label>
                    <input type="text" name="titre_ar" id="titreAr" placeholder="{{ __('messages.enter_course_title_ar') }}" class="form-control" required>
                    <div id="titre_ar-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Short Description (English) -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.short_description_en') }} *</label>
                    <textarea name="description_en" id="descriptionEn" rows="4" placeholder="{{ __('messages.enter_short_description_en') }}" class="form-control" required></textarea>
                    <div id="description_en-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Short Description (Arabic) -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.short_description_ar') }} *</label>
                    <textarea name="description_ar" id="descriptionAr" rows="4" placeholder="{{ __('messages.enter_short_description_ar') }}" class="form-control" required></textarea>
                    <div id="description_ar-error" class="invalid-feedback" style="display: none;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course Details Section -->
            <div class="col-12">
              <div class="rounded-16 bg-light-4 p-30">
                <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.course_details') }}</h5>
                
                <div class="row y-gap-30">
                  <!-- Category -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.category') }} *</label>
                    <select name="categories_id" id="category" class="form-control" required>
                      <option value="">{{ __('messages.select_category') }}</option>
                      @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->titre }}</option>
                      @endforeach
                    </select>
                    <div id="categories_id-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Teacher -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.teacher') }} *</label>
                    <select name="teacher_id" id="teacher" class="form-control" required>
                      <option value="">{{ __('messages.select_teacher') }}</option>
                      @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->nom }}</option>
                      @endforeach
                    </select>
                    <div id="teacher_id-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Country -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.country') }} *</label>
                    <select name="country_id" id="country" class="form-control" required>
                      <option value="">{{ __('messages.select_country') }}</option>
                      @foreach($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->nom }}</option>
                      @endforeach
                    </select>
                    <div id="country_id-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Language -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.language') }} *</label>
                    <select name="langue" id="langue" class="form-control" required>
                      <option value="">{{ __('messages.select_language') }}</option>
                      <option value="en">{{ __('messages.english') }}</option>
                      <option value="ar">{{ __('messages.arabic') }}</option>
                    </select>
                    <div id="langue-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Period -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.period') }} *</label>
                    <input type="text" name="period" id="period" placeholder="{{ __('messages.enter_period') }}" class="form-control" required>
                    <div id="period-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Points -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.points') }} *</label>
                    <input type="number" name="point" id="points" placeholder="{{ __('messages.enter_points') }}" class="form-control" min="0" step="0.1" required>
                    <div id="point-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Price -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.price') }} *</label>
                    <input type="number" name="prix" id="price" placeholder="{{ __('messages.enter_price') }}" class="form-control" min="0" step="0.01" required>
                    <div id="prix-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Promo Points -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.promo_points') }}</label>
                    <input type="number" name="promo_points" id="promoPoints" placeholder="{{ __('messages.enter_promo_points') }}" class="form-control" min="0" step="0.1">
                    <div id="promo_points-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Video -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.video') }}</label>
                    <input type="text" name="video" id="video" placeholder="{{ __('messages.enter_video_url') }}" class="form-control">
                    <div id="video-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Government -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.government') }}</label>
                    <input type="text" name="goverrnement" id="government" placeholder="{{ __('messages.enter_government') }}" class="form-control">
                    <div id="goverrnement-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- Start Date -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.start_date') }} *</label>
                    <input type="date" name="date_debut" id="startDate" class="form-control" required>
                    <div id="date_debut-error" class="invalid-feedback" style="display: none;"></div>
                  </div>

                  <!-- End Date -->
                  <div class="col-md-6">
                    <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.end_date') }} *</label>
                    <input type="date" name="date_fin" id="endDate" class="form-control" required>
                    <div id="date_fin-error" class="invalid-feedback" style="display: none;"></div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Course Options Section -->
            <div class="col-12">
              <div class="rounded-16 bg-light-4 p-30">
                <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.course_options') }}</h5>
                
                <div class="row y-gap-30">
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="statut" id="status" value="1" checked>
                      <label class="form-check-label" for="status">
                        {{ __('messages.active') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="online" id="online" value="1">
                      <label class="form-check-label" for="online">
                        {{ __('messages.online') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="classroom" id="classroom" value="1">
                      <label class="form-check-label" for="classroom">
                        {{ __('messages.classroom') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_exam_video" id="examVideo" value="1">
                      <label class="form-check-label" for="examVideo">
                        {{ __('messages.exam_video') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_stage" id="stage" value="1">
                      <label class="form-check-label" for="stage">
                        {{ __('messages.stage') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_classroom" id="isClassroom" value="1">
                      <label class="form-check-label" for="isClassroom">
                        {{ __('messages.is_classroom') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_zoom" id="zoom" value="1">
                      <label class="form-check-label" for="zoom">
                        {{ __('messages.zoom') }}
                      </label>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="iscach" id="cached" value="1">
                      <label class="form-check-label" for="cached">
                        {{ __('messages.cached') }}
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="col-12">
              <div class="d-flex items-center justify-end">
                <button type="button" class="button -md -light-1 mr-20" onclick="resetForm()">
                  {{ __('messages.reset') }}
                </button>
                <button type="submit" class="button -md -purple-1 text-white" id="submitBtn">
                  <i class="icon-plus mr-10"></i>
                  {{ __('messages.create_course') }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createCourseForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Form validation before submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        clearErrors();
        
        // Validate required fields
        const requiredFields = [
            'image', 'titre_en', 'titre_ar', 'description_en', 'description_ar',
            'categories_id', 'teacher_id', 'country_id', 'langue', 'period',
            'point', 'prix', 'date_debut', 'date_fin'
        ];
        
        let isValid = true;
        let missingFields = [];
        
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element || !element.value.trim()) {
                isValid = false;
                missingFields.push(getFieldLabel(field));
                showFieldError(field, '{{ __("messages.field_required") }}');
            }
        });
        
        // Validate dates
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        
        if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
            isValid = false;
            showFieldError('date_fin', '{{ __("messages.end_date_must_be_after_start_date") }}');
        }
        
        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: '{{ __("messages.validation_error") }}',
                html: `
                    <div class="text-left">
                        <p><strong>{{ __("messages.please_fill_required_fields") }}:</strong></p>
                        <ul class="text-left mt-10">
                            ${missingFields.map(field => `<li>${field}</li>`).join('')}
                        </ul>
                    </div>
                `,
                confirmButtonText: '{{ __("messages.ok") }}',
                confirmButtonColor: '#7c3aed'
            });
            return;
        }
        
        // Show confirmation dialog
        Swal.fire({
            title: '{{ __("messages.confirm_course_creation") }}',
            text: '{{ __("messages.are_you_sure_create_course") }}',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("messages.yes_create") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });
    
    function submitForm() {
        const formData = new FormData(form);
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="icon-spinner mr-10"></i>{{ __("messages.creating") }}...';
        
        // Show loading
        Swal.fire({
            title: '{{ __("messages.creating_course") }}',
            text: '{{ __("messages.please_wait") }}',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch('{{ route("admin.products.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("messages.success") }}',
                    text: data.message,
                    confirmButtonText: '{{ __("messages.ok") }}',
                    confirmButtonColor: '#7c3aed'
                }).then(() => {
                    window.location.href = data.redirect;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("messages.error") }}',
                    text: data.message,
                    confirmButtonText: '{{ __("messages.ok") }}',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '{{ __("messages.error") }}',
                text: '{{ __("messages.unexpected_error_occurred") }}',
                confirmButtonText: '{{ __("messages.ok") }}',
                confirmButtonColor: '#ef4444'
            });
        })
        .finally(() => {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="icon-plus mr-10"></i>{{ __("messages.create_course") }}';
        });
    }
    
    function clearErrors() {
        const errorElements = document.querySelectorAll('.invalid-feedback');
        errorElements.forEach(element => {
            element.style.display = 'none';
        });
        
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(element => {
            element.classList.remove('is-invalid');
        });
    }
    
    function showFieldError(fieldName, message) {
        const element = document.getElementById(fieldName);
        const errorElement = document.getElementById(fieldName + '-error');
        
        if (element) {
            element.classList.add('is-invalid');
        }
        
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
    
    function getFieldLabel(fieldName) {
        const labels = {
            'image': '{{ __("messages.course_image") }}',
            'titre_en': '{{ __("messages.course_title_en") }}',
            'titre_ar': '{{ __("messages.course_title_ar") }}',
            'description_en': '{{ __("messages.short_description_en") }}',
            'description_ar': '{{ __("messages.short_description_ar") }}',
            'categories_id': '{{ __("messages.category") }}',
            'teacher_id': '{{ __("messages.teacher") }}',
            'country_id': '{{ __("messages.country") }}',
            'langue': '{{ __("messages.language") }}',
            'period': '{{ __("messages.period") }}',
            'point': '{{ __("messages.points") }}',
            'prix': '{{ __("messages.price") }}',
            'date_debut': '{{ __("messages.start_date") }}',
            'date_fin': '{{ __("messages.end_date") }}'
        };
        
        return labels[fieldName] || fieldName;
    }
    
    // Reset form function
    window.resetForm = function() {
        Swal.fire({
            title: '{{ __("messages.confirm_reset") }}',
            text: '{{ __("messages.are_you_sure_reset_form") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '{{ __("messages.yes_reset") }}',
            cancelButtonText: '{{ __("messages.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                form.reset();
                clearErrors();
                Swal.fire({
                    icon: 'success',
                    title: '{{ __("messages.form_reset") }}',
                    text: '{{ __("messages.form_has_been_reset") }}',
                    confirmButtonText: '{{ __("messages.ok") }}',
                    confirmButtonColor: '#7c3aed'
                });
            }
        });
    };
});
</script>
@endsection
