@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Edit Student</h1>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back to List
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-warning text-dark">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="edit" class="me-2"></i>
                                        <h4 class="card-title mb-1">Edit Student</h4>
                                        <p class="text-dark-50 mb-0 ms-3">Edit student information</p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if($errors->any())
                                        <div class="alert alert-danger modern-alert">
                                            <div class="d-flex align-items-center">
                                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                                <div>
                                                    <strong>Validation Errors</strong>
                                                    <ul class="mb-0 mt-1">
                                                        @foreach($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="row">
                                            <!-- Personal Information -->
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="first_name" class="form-label">
                                                        <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        First Name *
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('first_name') is-invalid @enderror" 
                                                           id="first_name" 
                                                           name="first_name" 
                                                           value="{{ old('first_name', $student->first_name) }}" 
                                                           placeholder="Enter first name" 
                                                           required>
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="last_name" class="form-label">
                                                        <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        Last Name *
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('last_name') is-invalid @enderror" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           value="{{ old('last_name', $student->last_name) }}" 
                                                           placeholder="Enter last name" 
                                                           required>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">
                                                        <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        Email Address *
                                                    </label>
                                                    <input type="email" 
                                                           class="form-control @error('email') is-invalid @enderror" 
                                                           id="email" 
                                                           name="email" 
                                                           value="{{ old('email', $student->email) }}" 
                                                           placeholder="Enter email address" 
                                                           required>
                                                    @error('email')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label">
                                                        <i data-feather="phone" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        Phone Number
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('phone') is-invalid @enderror" 
                                                           id="phone" 
                                                           name="phone" 
                                                           value="{{ old('phone', $student->phone) }}" 
                                                           placeholder="Enter phone number">
                                                    @error('phone')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">
                                                        <i data-feather="lock" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        New Password
                                                    </label>
                                                    <input type="password" 
                                                           class="form-control @error('password') is-invalid @enderror" 
                                                           id="password" 
                                                           name="password" 
                                                           placeholder="Enter new password (leave blank to keep current)">
                                                    @error('password')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">Leave blank to keep current password</div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="country" class="form-label">
                                                        <i data-feather="globe" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        Country
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control @error('country') is-invalid @enderror" 
                                                           id="country" 
                                                           name="country" 
                                                           value="{{ old('country', $student->country) }}" 
                                                           placeholder="Enter country">
                                                    @error('country')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="available_points" class="form-label">
                                                        <i data-feather="award" class="me-2" style="width: 16px; height: 16px;"></i>
                                                        Available points
                                                    </label>
                                                    <input type="number"
                                                           class="form-control @error('available_points') is-invalid @enderror"
                                                           id="available_points"
                                                           name="available_points"
                                                           value="{{ old('available_points', $pointsBalance['available_points']) }}"
                                                           min="0"
                                                           max="10000000"
                                                           step="1"
                                                           required>
                                                    @error('available_points')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        Redeemable points (including those earned from course purchases). Change this value to set the student&rsquo;s current balance; an adjustment is logged.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="image" class="form-label">
                                                <i data-feather="image" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Profile Photo
                                            </label>
                                            <input type="file" 
                                                   class="form-control @error('image') is-invalid @enderror" 
                                                   id="image" 
                                                   name="image" 
                                                   accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Accepted formats: JPG, PNG, GIF, WEBP. Max size: 2MB</div>
                                            
                                            @if($student->image)
                                                <div class="mt-2">
                                                    <label class="form-label small">Current Photo:</label>
                                                    <div class="d-flex align-items-center">
                                                        <img src="{{ $student->image_url }}" 
                                                             alt="{{ $student->full_name }}" 
                                                             class="rounded-circle me-2"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                        <span class="text-muted">{{ $student->image }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-end gap-2 mt-4">
                                            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                                                <i data-feather="arrow-left" class="me-2"></i>
                                                Back
                                            </a>
                                            <button type="submit" class="btn btn-warning">
                                                <i data-feather="save" class="me-2"></i>
                                                Update Student
                                            </button>
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
</div>
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();
    
    // Preview image upload
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You can add image preview functionality here if needed
                console.log('New image selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
