@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="edit" class="text-white" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1">Edit Teacher</h4>
                            <p class="text-white-50 mb-0">Edit teacher information</p>
                        </div>
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

                    <form method="POST" action="{{ route('teachers.update', $teacher) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="nom" class="form-label">
                                        <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Last Name
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $teacher->nom) }}" 
                                           placeholder="Enter last name" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="prenom" class="form-label">
                                        <i data-feather="user" class="me-2" style="width: 16px; height: 16px;"></i>
                                        First Name
                                    </label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" value="{{ old('prenom', $teacher->prenom) }}" 
                                           placeholder="Enter first name" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="nom_en" class="form-label">
                                        <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Last Name (EN)
                                    </label>
                                    <input type="text" class="form-control @error('nom_en') is-invalid @enderror" 
                                           id="nom_en" name="nom_en" value="{{ old('nom_en', $teacher->nom_en) }}" 
                                           placeholder="Enter last name in English">
                                    @error('nom_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="prenom_en" class="form-label">
                                        <i data-feather="type" class="me-2" style="width: 16px; height: 16px;"></i>
                                        First Name (EN)
                                    </label>
                                    <input type="text" class="form-control @error('prenom_en') is-invalid @enderror" 
                                           id="prenom_en" name="prenom_en" value="{{ old('prenom_en', $teacher->prenom_en) }}" 
                                           placeholder="Enter first name in English">
                                    @error('prenom_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                Email
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $teacher->email) }}" 
                                   placeholder="Enter teacher's email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="image" class="form-label">
                                <i data-feather="image" class="me-2" style="width: 16px; height: 16px;"></i>
                                Profile Photo
                            </label>
                            <div class="mb-2">
                                <img src="{{ $teacher->image_url }}" alt="Current photo" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;" />
                            </div>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            <div class="form-text">Upload a new file to replace the current photo. Max size: 2MB</div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                                <i data-feather="arrow-left" class="me-2"></i>
                                Back
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i data-feather="save" class="me-2"></i>
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
