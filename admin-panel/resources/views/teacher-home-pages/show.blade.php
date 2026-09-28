@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Teacher Details</h3>
                    <div class="btn-group">
                        <a href="{{ route('teacher-home-pages.edit', $teacherHomePage->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit Teacher
                        </a>
                        <a href="{{ route('teacher-home-pages.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Teacher Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Teacher Information</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="40%"><strong>ID:</strong></td>
                                    <td>{{ $teacherHomePage->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name (Arabic):</strong></td>
                                    <td dir="rtl">{{ $teacherHomePage->name_ar }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Name (English):</strong></td>
                                    <td>{{ $teacherHomePage->name_en }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Display Order:</strong></td>
                                    <td><span class="badge bg-info">{{ $teacherHomePage->order }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($teacherHomePage->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Created:</strong></td>
                                    <td>{{ $teacherHomePage->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Last Updated:</strong></td>
                                    <td>{{ $teacherHomePage->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Teacher Image</h5>
                            @if($teacherHomePage->image)
                                <div class="text-center">
                                    <img src="{{ $teacherHomePage->image_url }}" 
                                         alt="{{ $teacherHomePage->name_en }}" 
                                         class="img-thumbnail" 
                                         style="max-width: 100%; max-height: 400px; object-fit: cover;">
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle me-2"></i>
                                    No image uploaded
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('teacher-home-pages.edit', $teacherHomePage->id) }}" class="btn btn-primary">
                                            <i class="fa fa-edit"></i> Edit Teacher
                                        </a>
                                        
                                        <form action="{{ route('teacher-home-pages.destroy', $teacherHomePage->id) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this teacher?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fa fa-trash"></i> Delete Teacher
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

<style>
.card {
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #6e6b7b;
}

.table td {
    vertical-align: middle;
}
</style>
@endsection

