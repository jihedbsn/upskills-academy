@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="plus" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">New Category</h4>
                            <p class="text-white-50 mb-0">Create a new product category</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="alert-triangle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <div>
                                    <strong>Validation errors</strong>
                                    <ul class="mb-0 mt-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="titre" class="form-label">
                                <i data-feather="tag" class="me-2" style="width: 16px; height: 16px;"></i>
                                Category title (French)
                            </label>
                            <input type="text" class="form-control @error('titre') is-invalid @enderror" 
                                   id="titre" name="titre" value="{{ old('titre') }}" 
                                   placeholder="Enter the category title in French" required>
                            @error('titre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="titre_en" class="form-label">
                                <i data-feather="globe" class="me-2" style="width: 16px; height: 16px;"></i>
                                Category title (English)
                            </label>
                            <input type="text" class="form-control @error('titre_en') is-invalid @enderror" 
                                   id="titre_en" name="titre_en" value="{{ old('titre_en') }}" 
                                   placeholder="Enter category title in English">
                            @error('titre_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="titre_ar" class="form-label">
                                <i data-feather="globe" class="me-2" style="width: 16px; height: 16px;"></i>
                                Category title (Arabic)
                            </label>
                            <input type="text" class="form-control @error('titre_ar') is-invalid @enderror" 
                                   id="titre_ar" name="titre_ar" value="{{ old('titre_ar') }}" 
                                   placeholder="أدخل عنوان الفئة بالعربية" dir="rtl">
                            @error('titre_ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2"></i>
                                Create category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
