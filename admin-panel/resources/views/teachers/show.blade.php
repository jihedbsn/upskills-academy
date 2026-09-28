@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
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
                                <h4 class="card-title mb-1">Teacher Details</h4>
                                <p class="text-white-50 mb-0">Complete teacher information</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit
                            </a>
                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this teacher?')">
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
                <div class="card-body">
                    <div class="mb-4 d-flex align-items-center">
                        <img src="{{ $teacher->image_url }}" alt="Photo" style="width: 96px; height: 96px; object-fit: cover; border-radius: 12px; margin-right: 16px;" />
                        <div>
                            <div class="fw-bold h5 mb-0">{{ $teacher->nom }}</div>
                            <small class="text-muted">Teacher</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="hash" class="me-2" style="width: 16px; height: 16px;"></i>
                                    ID
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Full Name
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->nom }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Email
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->email }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="phone" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Phone
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->telephone ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="map-pin" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Address
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->adresse ?? 'Not provided' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Creation Date
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Last Modified
                                </label>
                                <p class="form-control-plaintext">{{ $teacher->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="info" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Status
                                </label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-success">Active</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Back to List
                        </a>
                        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-warning">
                            <i data-feather="edit" class="me-2"></i>
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
