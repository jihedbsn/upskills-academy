@extends('layouts.dashboard')

@section('content')
<div class="container-fluid">
  <div class="row y-gap-30">
    <div class="col-12">
      <div class="dashboard__content bg-white rounded-8 shadow-1">
        <div class="py-30 px-30">
          <div class="d-flex items-center justify-between mb-30">
            <h4 class="text-18 lh-1 fw-500">{{ __('messages.stage_submission_details') }}</h4>
            <a href="{{ route('admin.products.index') }}" class="button -md -light-1">
              <i class="icon-arrow-left mr-10"></i>
              {{ __('messages.back_to_courses') }}
            </a>
          </div>

          <div class="row y-gap-30">
            <!-- Informations de base -->
            <div class="col-12">
              <div class="rounded-16 bg-light-4 p-30">
                <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.basic_information') }}</h5>

                <div class="row y-gap-20">
                  <div class="col-md-6">
                    <div class="text-14 fw-500 text-dark-1 mb-5">{{ __('messages.student') }}</div>
                    <div class="text-16 text-dark-1">{{ $studentStageCourse->student->first_name }} {{ $studentStageCourse->student->last_name }}</div>
                    <div class="text-14 text-light-1">{{ $studentStageCourse->student->email }}</div>
                  </div>

                  <div class="col-md-6">
                    <div class="text-14 fw-500 text-dark-1 mb-5">{{ __('messages.course') }}</div>
                    <div class="text-16 text-dark-1">{{ $studentStageCourse->product->titre }}</div>
                  </div>

                  <div class="col-md-6">
                    <div class="text-14 fw-500 text-dark-1 mb-5">{{ __('messages.submitted_on') }}</div>
                    <div class="text-16 text-dark-1">{{ $studentStageCourse->submitted_at->format('d/m/Y H:i') }}</div>
                  </div>

                  <div class="col-md-6">
                    <div class="text-14 fw-500 text-dark-1 mb-5">{{ __('messages.status') }}</div>
                    <span class="badge bg-{{ $studentStageCourse->status_class }}">{{ $studentStageCourse->status_text }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Fichiers soumis -->
            <div class="col-12">
              <div class="rounded-16 bg-light-4 p-30">
                <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.submitted_files') }}</h5>

                <div class="row y-gap-30">
                  <!-- Fichier 1 -->
                  @if($studentStageCourse->file1)
                    <div class="col-md-6">
                      <div class="text-14 fw-500 text-dark-1 mb-10">{{ __('messages.file_1') }}</div>
                      <div class="d-flex items-center p-20 bg-white rounded-8 border">
                        @if($studentStageCourse->file1_type === 'image')
                          <img src="{{ $studentStageCourse->file1_url }}" alt="File 1" class="size-60 rounded-8 object-cover mr-15">
                        @else
                          <div class="size-60 rounded-8 bg-light-3 d-flex items-center justify-center mr-15">
                            <i class="icon-file text-20 text-light-1"></i>
                          </div>
                        @endif
                        <div class="flex-1">
                          <div class="text-14 fw-500 text-dark-1">{{ $studentStageCourse->file1 }}</div>
                          <div class="text-12 text-light-1">{{ strtoupper($studentStageCourse->file1_extension) }}</div>
                        </div>
                        <a href="{{ route('admin.student-stage-courses.download-file', [$studentStageCourse->id, 1]) }}" class="button -sm -blue-1">
                          <i class="icon-download"></i>
                        </a>
                      </div>
                    </div>
                  @endif

                  <!-- Fichier 2 -->
                  @if($studentStageCourse->file2)
                    <div class="col-md-6">
                      <div class="text-14 fw-500 text-dark-1 mb-10">{{ __('messages.file_2') }}</div>
                      <div class="d-flex items-center p-20 bg-white rounded-8 border">
                        @if($studentStageCourse->file2_type === 'image')
                          <img src="{{ $studentStageCourse->file2_url }}" alt="File 2" class="size-60 rounded-8 object-cover mr-15">
                        @else
                          <div class="size-60 rounded-8 bg-light-3 d-flex items-center justify-center mr-15">
                            <i class="icon-file text-20 text-light-1"></i>
                          </div>
                        @endif
                        <div class="flex-1">
                          <div class="text-14 fw-500 text-dark-1">{{ $studentStageCourse->file2 }}</div>
                          <div class="text-12 text-light-1">{{ strtoupper($studentStageCourse->file2_extension) }}</div>
                        </div>
                        <a href="{{ route('admin.student-stage-courses.download-file', [$studentStageCourse->id, 2]) }}" class="button -sm -blue-1">
                          <i class="icon-download"></i>
                        </a>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Description -->
            @if($studentStageCourse->description)
              <div class="col-12">
                <div class="rounded-16 bg-light-4 p-30">
                  <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.description') }}</h5>
                  <div class="text-15 text-dark-1 leading-relaxed">
                    {{ $studentStageCourse->description }}
                  </div>
                </div>
              </div>
            @endif

            <!-- Actions d'administration -->
            <div class="col-12">
              <div class="rounded-16 bg-light-4 p-30">
                <h5 class="text-17 lh-1 fw-500 mb-30">{{ __('messages.admin_actions') }}</h5>

                <form id="validationForm">
                  @csrf
                  <div class="row y-gap-20">
                    <div class="col-12">
                      <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.status') }} *</label>
                      <select name="is_valid" id="statusSelect" class="form-control" required>
                        <option value="0" {{ $studentStageCourse->is_valid === 0 ? 'selected' : '' }}>{{ __('messages.pending') }}</option>
                        <option value="1" {{ $studentStageCourse->is_valid === 1 ? 'selected' : '' }}>{{ __('messages.validated') }}</option>
                        <option value="-1" {{ $studentStageCourse->is_valid === -1 ? 'selected' : '' }}>{{ __('messages.rejected') }}</option>
                      </select>
                    </div>

                    <div class="col-12">
                      <label class="text-16 lh-1 fw-500 text-dark-1 mb-10">{{ __('messages.admin_notes') }}</label>
                      <textarea name="admin_notes" id="adminNotes" rows="4" class="form-control" placeholder="{{ __('messages.add_admin_notes') }}">{{ $studentStageCourse->admin_notes }}</textarea>
                    </div>

                    <div class="col-12">
                      <button type="submit" class="button -md -purple-1 text-white" id="updateStatusBtn">
                        <i class="icon-save mr-10"></i>
                        {{ __('messages.update_status') }}
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('validationForm');
    const submitBtn = document.getElementById('updateStatusBtn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Désactiver le bouton
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="icon-spinner mr-10"></i>{{ __("messages.updating") }}...';

        // Afficher le loading
        Swal.fire({
            title: '{{ __("messages.updating_status") }}',
            text: '{{ __("messages.please_wait") }}',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route("admin.student-stage-courses.update-validation", $studentStageCourse) }}', {
            method: 'PUT',
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
                    location.reload();
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
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="icon-save mr-10"></i>{{ __("messages.update_status") }}';
        });
    });
});
</script>
@endsection
