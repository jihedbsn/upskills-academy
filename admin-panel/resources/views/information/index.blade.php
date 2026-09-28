@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <div class="avatar avatar-lg bg-white rounded-circle d-flex align-items-center justify-content-center">
                                <i data-feather="info" class="text-primary" style="width: 24px; height: 24px;"></i>
                            </div>
                        </div>
                        <div>
                            <h4 class="card-title mb-1 text-white">Site Information</h4>
                            <p class="mb-0" style="opacity: 0.9;">Manage your website contact details and social media links</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success modern-alert">
                            <div class="d-flex align-items-center">
                                <i data-feather="check-circle" class="me-3" style="width: 20px; height: 20px;"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

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

                    <form method="POST" action="{{ route('information.update') }}">
                        @csrf
                        @method('PUT')

                        <!-- Contact Information Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i data-feather="phone" class="me-2"></i>
                                    Contact Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="email" class="form-label">
                                                <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Email
                                            </label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $information->email) }}" 
                                                   placeholder="contact@yoursite.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="phone" class="form-label">
                                                <i data-feather="phone" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Phone
                                            </label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone', $information->phone) }}" 
                                                   placeholder="+1 234 567 8900">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="adresse" class="form-label">
                                        <i data-feather="map-pin" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Address
                                    </label>
                                    <textarea class="form-control @error('adresse') is-invalid @enderror" 
                                              id="adresse" name="adresse" rows="3" 
                                              placeholder="Enter your full address">{{ old('adresse', $information->adresse) }}</textarea>
                                    @error('adresse')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i data-feather="share-2" class="me-2"></i>
                                    Social Media Links
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="facebook" class="form-label">
                                                <i data-feather="facebook" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Facebook
                                            </label>
                                            <input type="url" class="form-control @error('facebook') is-invalid @enderror" 
                                                   id="facebook" name="facebook" value="{{ old('facebook', $information->facebook) }}" 
                                                   placeholder="https://facebook.com/yourpage">
                                            @error('facebook')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="youtube" class="form-label">
                                                <i data-feather="youtube" class="me-2" style="width: 16px; height: 16px;"></i>
                                                YouTube
                                            </label>
                                            <input type="url" class="form-control @error('youtube') is-invalid @enderror" 
                                                   id="youtube" name="youtube" value="{{ old('youtube', $information->youtube) }}" 
                                                   placeholder="https://youtube.com/yourchannel">
                                            @error('youtube')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-4">
                                            <label for="instagram" class="form-label">
                                                <i data-feather="instagram" class="me-2" style="width: 16px; height: 16px;"></i>
                                                Instagram
                                            </label>
                                            <input type="url" class="form-control @error('instagram') is-invalid @enderror" 
                                                   id="instagram" name="instagram" value="{{ old('instagram', $information->instagram) }}" 
                                                   placeholder="https://instagram.com/yourprofile">
                                            @error('instagram')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i data-feather="save" class="me-2"></i>
                                Update Information
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

