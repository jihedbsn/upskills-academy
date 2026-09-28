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
                                    <i data-feather="folder" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="card-title mb-1">Category Details</h4>
                                <p class="text-white-50 mb-0">Complete information about the category</p>
                            </div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                                <i data-feather="edit" class="me-2"></i>
                                Edit
                            </a>
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="hash" class="me-2" style="width: 16px; height: 16px;"></i>
                                    ID
                                </label>
                                <p class="form-control-plaintext">{{ $category->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="tag" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Title
                                </label>
                                <p class="form-control-plaintext">{{ $category->titre }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="calendar" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Created at
                                </label>
                                <p class="form-control-plaintext">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i data-feather="clock" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Last updated
                                </label>
                                <p class="form-control-plaintext">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                            <i data-feather="arrow-left" class="me-2"></i>
                            Back to list
                        </a>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
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
