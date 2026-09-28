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
                                <i data-feather="info" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">About Section Details</h4>
                            <p class="text-white-50 mb-0">Complete information about the "About" section</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="info" class="me-2"></i>
                                        General information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Section ID</label>
                                            <p class="mb-0">{{ $aboutAcceuil->id }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Status</label>
                                            <span class="badge {{ $aboutAcceuil->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $aboutAcceuil->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Created at</label>
                                            <p class="mb-0">{{ $aboutAcceuil->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label fw-bold">Last updated</label>
                                            <p class="mb-0">{{ $aboutAcceuil->updated_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description en Anglais -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Description in English
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="bg-light p-3 rounded">
                                        {{ $aboutAcceuil->description_en }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description en Arabe -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-white">
                                    <h5 class="mb-0">
                                        <i data-feather="globe" class="me-2"></i>
                                        Description in Arabic
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="bg-light p-3 rounded">
                                        {{ $aboutAcceuil->description_ar }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('about-acceuil.index') }}" class="btn btn-secondary">
                                    <i data-feather="arrow-left" class="me-2"></i>
                                    Back to list
                                </a>
                                <a href="{{ route('about-acceuil.edit', $aboutAcceuil) }}" class="btn btn-warning">
                                    <i data-feather="edit" class="me-2"></i>
                                    Edit
                                </a>
                                <form action="{{ route('about-acceuil.destroy', $aboutAcceuil) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this section?')">
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

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}

.bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
}
</style>
@endsection
