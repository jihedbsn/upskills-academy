@extends('student-dashboard.layouts.app')

@section('title', __('student_dashboard.profile'))

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student-dashboard.index') }}">{{ __('student_dashboard.dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('student_dashboard.profile') }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">{{ __('student_dashboard.profile') }}</h1>
            <p class="text-muted">{{ __('student_dashboard.manage_your_profile') }}</p>
        </div>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil me-2"></i>{{ __('student_dashboard.edit_profile') }}
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <img id="profilePhoto" src="{{ $student->image_url }}" class="rounded-circle" alt="{{ $student->full_name }}" style="width: 150px; height: 150px; object-fit: cover;" onerror="this.onerror=null;this.src='{{ asset('assets/img/students/default-avatar.png') }}'">
                    </div>
                    <h5 class="card-title">{{ $student->full_name }}</h5>
                    <p class="text-muted">{{ __('student_dashboard.student') }}</p>

                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <h6 class="mb-1">{{ $profileStats['courses_enrolled'] }}</h6>
                            <small class="text-muted">{{ __('student_dashboard.courses') }}</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-1">{{ $profileStats['certificates_earned'] }}</h6>
                            <small class="text-muted">{{ __('student_dashboard.certificates') }}</small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-success">{{ __('student_dashboard.active') }}</span>
                        <span class="badge bg-primary ms-1">{{ __('student_dashboard.premium') }}</span>
                    </div>

                    <button class="btn btn-outline-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#changePhotoModal">
                        <i class="bi bi-camera me-2"></i>{{ __('student_dashboard.change_photo') }}
                    </button>
                    <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="bi bi-key me-2"></i>{{ __('student_dashboard.change_password') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('student_dashboard.personal_information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.first_name') }}</label>
                            <input type="text" class="form-control" value="{{ $student->first_name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.last_name') }}</label>
                            <input type="text" class="form-control" value="{{ $student->last_name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.email') }}</label>
                            <input type="email" class="form-control" value="{{ $student->email }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.phone') }}</label>
                            <input type="tel" class="form-control" value="{{ $student->phone ?? __('student_dashboard.not_provided') }}" readonly>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">{{ __('student_dashboard.total_orders') }}</label>
                            <input type="text" class="form-control" value="{{ $profileStats['total_orders'] }}" readonly>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <!-- Deactivate Account Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-danger">
                <div class="card-header bg-danger bg-opacity-10">
                    <h5 class="card-title mb-0 text-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ __('student_dashboard.danger_zone') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">{{ __('student_dashboard.deactivate_account') }}</h6>
                            <p class="text-muted mb-0">{{ __('student_dashboard.deactivate_account_description') }}</p>
                        </div>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-person-x me-2"></i>{{ __('student_dashboard.deactivate_account') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deactivate Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger bg-opacity-10">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ __('student_dashboard.deactivate_account') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteAccountForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>{{ __('student_dashboard.warning') }}!</strong>
                        {{ __('student_dashboard.deactivate_account_warning') }}
                    </div>
                    <p>{{ __('student_dashboard.deactivate_account_confirm_text') }}</p>
                    <ul class="text-muted small">
                        <li>{{ __('student_dashboard.deactivate_data_access') }}</li>
                        <li>{{ __('student_dashboard.deactivate_data_login') }}</li>
                        <li>{{ __('student_dashboard.deactivate_data_preserved') }}</li>
                        <li>{{ __('student_dashboard.deactivate_data_reactivate') }}</li>
                    </ul>
                    <div class="mb-3">
                        <label class="form-label">{{ __('student_dashboard.confirm_password') }}</label>
                        <input type="password" id="deleteAccountPassword" name="password" class="form-control" placeholder="{{ __('student_dashboard.enter_password_to_confirm') }}" required>
                        <div id="deleteAccountError" class="text-danger mt-2" style="display: none;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.cancel') }}</button>
                    <button type="submit" id="deleteAccountBtn" class="btn btn-danger">
                        <span class="btn-text"><i class="bi bi-person-x me-1"></i>{{ __('student_dashboard.confirm_deactivate') }}</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> {{ __('student_dashboard.deactivating') }}...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.edit_profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="profileUpdateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.first_name') }}</label>
                            <input type="text" name="first_name" class="form-control" value="{{ $student->first_name }}" required pattern="[a-zA-Z\s\-']+" title="English letters only">
                            <small class="text-muted">English letters only</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.last_name') }}</label>
                            <input type="text" name="last_name" class="form-control" value="{{ $student->last_name }}" required pattern="[a-zA-Z\s\-']+" title="English letters only">
                            <small class="text-muted">English letters only</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.email') }}</label>
                            <input type="email" class="form-control" value="{{ $student->email }}" disabled>
                            <small class="text-muted">{{ __('login.cannot_change') }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('student_dashboard.phone') }}</label>
                            <input type="tel" name="phone" class="form-control" value="{{ $student->phone }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('login.birthdate') }}</label>
                            <input type="date" name="birthdate" class="form-control" value="{{ $student->birthdate ? $student->birthdate->format('Y-m-d') : '' }}" max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('login.gender') }}</label>
                            <select name="gender" class="form-select">
                                <option value="">{{ __('login.select_gender') }}</option>
                                <option value="male" {{ $student->gender == 'male' ? 'selected' : '' }}>{{ __('login.male') }}</option>
                                <option value="female" {{ $student->gender == 'female' ? 'selected' : '' }}>{{ __('login.female') }}</option>
                            </select>
                        </div>
                    </div>
                </form>
                <div id="profileUpdateError" class="text-danger mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="updateProfile()">{{ __('student_dashboard.save_changes') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Photo Modal -->
<div class="modal fade" id="changePhotoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.change_photo') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="photoUploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="currentPhoto" src="{{ $student->image_url }}" class="rounded-circle" alt="Current Photo" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>

                    <!-- Upload New Photo -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.upload_new_photo') }}</label>
                        <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*" onchange="previewPhoto(this)">
                        <small class="text-muted">{{ __('messages.max_file_size_2mb') }}</small>
                        <div id="photoError" class="text-danger mt-2" style="display: none;"></div>
                    </div>

                    <!-- Or Use Gravatar -->
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.or_use_gravatar') }}</label>
                        <input type="email" id="gravatarEmail" name="gravatar_email" class="form-control" placeholder="{{ __('messages.enter_gravatar_email') }}" value="{{ $student->email }}">
                        <small class="text-muted">{{ __('messages.gravatar_description') }}</small>
                    </div>

                    <!-- Photo Preview -->
                    <div id="photoPreview" class="text-center mb-3" style="display: none;">
                        <h6>{{ __('messages.preview') }}</h6>
                        <img id="previewImage" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.cancel') }}</button>
                    <button type="submit" id="uploadBtn" class="btn btn-primary">
                        <span class="btn-text">{{ __('messages.upload_photo') }}</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> {{ __('messages.uploading') }}...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('student_dashboard.change_password') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordChangeForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.current_password') }}</label>
                        <input type="password" id="currentPassword" name="current_password" class="form-control" placeholder="{{ __('messages.enter_current_password') }}" required>
                        <div id="currentPasswordError" class="text-danger mt-2" style="display: none;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.new_password') }}</label>
                        <input type="password" id="newPassword" name="new_password" class="form-control" placeholder="{{ __('messages.enter_new_password') }}" required>
                        <div id="newPasswordError" class="text-danger mt-2" style="display: none;"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.confirm_new_password') }}</label>
                        <input type="password" id="confirmPassword" name="confirm_password" class="form-control" placeholder="{{ __('messages.confirm_new_password') }}" required>
                        <div id="confirmPasswordError" class="text-danger mt-2" style="display: none;"></div>
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="bi bi-info-circle me-2"></i>
                            {{ __('messages.password_requirements') }}
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('student_dashboard.cancel') }}</button>
                    <button type="submit" id="changePasswordBtn" class="btn btn-primary">
                        <span class="btn-text">{{ __('student_dashboard.change_password') }}</span>
                        <span class="btn-loading" style="display: none;">
                            <i class="fa fa-spinner fa-spin"></i> {{ __('messages.changing') }}...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    // Test function for debugging
    window.testPhotoRoute = function() {
        console.log('Testing photo route...');
        fetch('{{ route("student-dashboard.profile.test-photo") }}', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Test route response:', data);
            alert('Test route working! Check console for details.');
        })
        .catch(error => {
            console.error('Test route error:', error);
            alert('Test route failed! Check console for details.');
        });
    };

    document.addEventListener('DOMContentLoaded', function() {
    // Delete Account functionality
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    const deleteAccountError = document.getElementById('deleteAccountError');

    deleteAccountForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const password = document.getElementById('deleteAccountPassword').value;
        if (!password) {
            deleteAccountError.textContent = '{{ __("student_dashboard.password_required") }}';
            deleteAccountError.style.display = 'block';
            return;
        }

        // Show loading state
        const btnText = deleteAccountBtn.querySelector('.btn-text');
        const btnLoading = deleteAccountBtn.querySelector('.btn-loading');
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        deleteAccountBtn.disabled = true;
        deleteAccountError.style.display = 'none';

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const formData = new FormData();
        formData.append('password', password);
        formData.append('_token', csrfToken);

        fetch('{{ route("student-dashboard.profile.delete-account") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Redirect to home page
                window.location.href = data.redirect || '/';
            } else {
                deleteAccountError.textContent = data.message || '{{ __("student_dashboard.account_deactivation_failed") }}';
                deleteAccountError.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            deleteAccountError.textContent = '{{ __("student_dashboard.account_deactivation_failed") }}';
            deleteAccountError.style.display = 'block';
        })
        .finally(() => {
            btnText.style.display = 'inline';
            btnLoading.style.display = 'none';
            deleteAccountBtn.disabled = false;
        });
    });

    // Photo upload functionality
    const photoUploadForm = document.getElementById('photoUploadForm');
    const photoInput = document.getElementById('photoInput');
    const gravatarEmail = document.getElementById('gravatarEmail');
    const uploadBtn = document.getElementById('uploadBtn');

    // Password change functionality
    const passwordChangeForm = document.getElementById('passwordChangeForm');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const photoError = document.getElementById('photoError');

    // Preview photo when file is selected
    window.previewPhoto = function(input) {
        const file = input.files[0];
        const previewDiv = document.getElementById('photoPreview');
        const previewImage = document.getElementById('previewImage');

        // Hide error message
        photoError.style.display = 'none';

        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showPhotoError('{{ __("messages.invalid_file_type") }}');
                input.value = '';
                return;
            }

            // Validate file size (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                showPhotoError('{{ __("messages.file_too_large") }}');
                input.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewDiv.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.style.display = 'none';
        }
    };

    // Show error message
    function showPhotoError(message) {
        photoError.textContent = message;
        photoError.style.display = 'block';
    }

    // Handle form submission
    photoUploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        const file = photoInput.files[0];
        const email = gravatarEmail.value.trim();

        // Show loading state
        const btnText = uploadBtn.querySelector('.btn-text');
        const btnLoading = uploadBtn.querySelector('.btn-loading');

        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        uploadBtn.disabled = true;

        // Determine upload method
        if (file) {
            // Upload file
            formData.append('photo', file);
            formData.append('upload_type', 'file');
        } else if (email) {
            // Use Gravatar
            formData.append('gravatar_email', email);
            formData.append('upload_type', 'gravatar');
        } else {
            showPhotoError('{{ __("messages.please_select_photo_or_email") }}');
            resetButton();
            return;
        }

        // Send AJAX request
        console.log('Sending request to:', '{{ route("student-dashboard.profile.update-photo") }}');
        console.log('FormData contents:', formData);
        console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch('{{ route("student-dashboard.profile.update-photo") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                // Update both photos - modal and main profile photo
                document.getElementById('currentPhoto').src = data.photo_url;
                document.getElementById('profilePhoto').src = data.photo_url;

                // Show success message
                showSuccessMessage('{{ __("messages.photo_uploaded_successfully") }}');

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('changePhotoModal'));
                modal.hide();

                // Reset form
                photoUploadForm.reset();
                document.getElementById('photoPreview').style.display = 'none';
                photoError.style.display = 'none';
            } else {
                showPhotoError(data.message || '{{ __("messages.photo_upload_failed") }}');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showPhotoError('{{ __("messages.photo_upload_failed") }}');
        })
        .finally(() => {
            resetButton();
        });
    });

    // Reset button state
    function resetButton() {
        const btnText = uploadBtn.querySelector('.btn-text');
        const btnLoading = uploadBtn.querySelector('.btn-loading');

        btnText.style.display = 'inline';
        btnLoading.style.display = 'none';
        uploadBtn.disabled = false;
    }

    // Show success message
    function showSuccessMessage(message) {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0';
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        // Add to page
        document.body.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove after hiding
        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    }

    // Function to update profile - must be in global scope for onclick
    window.updateProfile = function() {
        const form = document.getElementById('profileUpdateForm');
        const formData = new FormData(form);
        const errorDiv = document.getElementById('profileUpdateError');
        const saveBtn = document.querySelector('#editProfileModal .btn-primary');

        errorDiv.style.display = 'none';
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        fetch('{{ route("student-dashboard.profile.update") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(data.message || 'Profile updated successfully!');

                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editProfileModal'));
                modal.hide();

                // Reload page to show updated data
                location.reload();
            } else {
                errorDiv.textContent = data.message || 'Failed to update profile';
                errorDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorDiv.textContent = 'An error occurred while updating profile';
            errorDiv.style.display = 'block';
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.textContent = '{{ __("student_dashboard.save_changes") }}';
        });
    }
});
</script>
