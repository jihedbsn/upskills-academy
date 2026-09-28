@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="eye" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="page-title">Subject Details</h1>
                            <p class="text-muted mb-0">Subject #{{ $sujet->id }}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="info" class="me-2"></i>
                                        Informations du Sujet
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Subject ID</label>
                                                <p class="form-control-plaintext">{{ $sujet->id }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Type</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge bg-{{ $sujet->type_color }} fs-6">
                                                        {{ $sujet->type_name }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Language</label>
                                                <p class="form-control-plaintext">
                                                    <span class="badge {{ $sujet->lang === 'ar' ? 'bg-success' : 'bg-info' }} fs-6">
                                                        {{ $sujet->language_name }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Created</label>
                                                <p class="form-control-plaintext">
                                                    <i data-feather="calendar" class="me-1"></i>
                                                    {{ $sujet->created_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Last Updated</label>
                                                <p class="form-control-plaintext">
                                                    <i data-feather="calendar" class="me-1"></i>
                                                    {{ $sujet->updated_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h5 class="card-title mb-0">
                                        <i data-feather="file-text" class="me-2"></i>
                                        Subject Description
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="description-content">
                                        @if($sujet->lang === 'ar')
                                            <div class="text-end" dir="rtl">
                                                <p class="fs-5 lh-lg">{{ $sujet->description }}</p>
                                            </div>
                                        @else
                                            <div class="text-start">
                                                <p class="fs-5 lh-lg">{{ $sujet->description }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i data-feather="hash" class="me-1"></i>
                                            {{ strlen($sujet->description) }} characters
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('sujets.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Back to List
                                </a>
                                <div class="btn-group">
                                    <a href="{{ route('sujets.edit', $sujet) }}" class="btn btn-warning">
                                        <i data-feather="edit" class="me-2"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('sujets.destroy', $sujet) }}" 
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this subject?')">
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
@endsection

@push('scripts')
<script>
    // Initialize Feather icons
    feather.replace();
</script>
@endpush

<style>
.description-content {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 2rem;
    border-left: 4px solid #007bff;
}

.description-content p {
    margin-bottom: 0;
    color: #495057;
}

[dir="rtl"] {
    text-align: right;
}

.card.border-0.shadow-sm {
    border: 1px solid #e9ecef !important;
}
</style>
