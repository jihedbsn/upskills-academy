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
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="user" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Student Details</h1>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">
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
                                <div class="card-header bg-info text-white">
                                    <div class="d-flex align-items-center">
                                        <i data-feather="user" class="me-2"></i>
                                        <h4 class="card-title mb-1">Student Details</h4>
                                        <p class="text-white-50 mb-0 ms-3">Complete student information</p>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Profile Photo -->
                                        <div class="col-md-4 text-center mb-4">
                                            <div class="mb-3">
                                                @if($student->image)
                                                    <img src="{{ $student->image_url }}" 
                                                         alt="{{ $student->full_name }}" 
                                                         class="rounded-circle shadow-sm"
                                                         style="width: 150px; height: 150px; object-fit: cover;">
                                                @else
                                                    <div class="avatar avatar-lg bg-light text-dark rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                                         style="width: 150px; height: 150px;">
                                                        <i data-feather="user" style="width: 60px; height: 60px;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <h5 class="mb-1">{{ $student->full_name }}</h5>
                                            <p class="text-muted">Student ID: #{{ $student->id }}</p>
                                        </div>

                                        <!-- Student Information -->
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            First Name
                                                        </label>
                                                        <p class="form-control-plaintext">{{ $student->first_name }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Last Name
                                                        </label>
                                                        <p class="form-control-plaintext">{{ $student->last_name }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Email Address
                                                        </label>
                                                        <p class="form-control-plaintext">
                                                            <a href="mailto:{{ $student->email }}" class="text-primary">
                                                                {{ $student->email }}
                                                            </a>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="phone" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Phone Number
                                                        </label>
                                                        <p class="form-control-plaintext">
                                                            @if($student->phone)
                                                                <a href="tel:{{ $student->phone }}" class="text-primary">
                                                                    {{ $student->phone }}
                                                                </a>
                                                            @else
                                                                <span class="text-muted">Not provided</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="globe" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Country
                                                        </label>
                                                        <p class="form-control-plaintext">
                                                            @if($student->country)
                                                                <span class="badge bg-info">{{ $student->country }}</span>
                                                            @else
                                                                <span class="text-muted">Not specified</span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Registration Date
                                                        </label>
                                                        <p class="form-control-plaintext">{{ $student->created_at->format('d/m/Y H:i') }}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Last Updated
                                                        </label>
                                                        <p class="form-control-plaintext">{{ $student->updated_at->format('d/m/Y H:i') }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">
                                                            <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                                            Email Status
                                                        </label>
                                                        <p class="form-control-plaintext">
                                                            @if($student->email_verified_at)
                                                                <span class="badge bg-success">
                                                                    <i data-feather="check" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                    Verified
                                                                </span>
                                                            @else
                                                                <span class="badge bg-warning">
                                                                    <i data-feather="alert-circle" class="me-1" style="width: 12px; height: 12px;"></i>
                                                                    Not Verified
                                                                </span>
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="border-top pt-4 mt-4">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    <i data-feather="info" class="me-1" style="width: 14px; height: 14px;"></i>
                                                    Student created {{ $student->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                                                    <i data-feather="arrow-left" class="me-2"></i>
                                                    Back to List
                                                </a>
                                                <a href="{{ route('students.edit', $student) }}" class="btn btn-warning">
                                                    <i data-feather="edit" class="me-2"></i>
                                                    Edit
                                                </a>
                                                <form action="{{ route('students.destroy', $student) }}" 
                                                      method="POST" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this student?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i data-feather="trash-2" class="me-2"></i>
                                                        Delete
                                                    </button>
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
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();
</script>
@endpush
