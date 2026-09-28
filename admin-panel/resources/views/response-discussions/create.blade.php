@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Créer une nouvelle réponse</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.response-discussions.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                @if($preselectedDiscussion)
                                    <!-- If discussion is pre-selected, show it as a display field and use hidden input -->
                                    <label for="discussion_display" class="form-label">Discussion <span class="text-danger">*</span></label>
                                    <div class="form-control-plaintext border rounded p-2 bg-light">
                                        <strong>{{ $preselectedDiscussion->product ? $preselectedDiscussion->product->titre : 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">Student: {{ $preselectedDiscussion->student ? $preselectedDiscussion->student->full_name : 'Student Deleted' }}</small>
                                    </div>
                                    <input type="hidden" name="discussion_id" value="{{ $preselectedDiscussion->id }}">
                                @else
                                    <!-- If no discussion is pre-selected, show dropdown -->
                                    <label for="discussion_id" class="form-label">Discussion <span class="text-danger">*</span></label>
                                    <select class="form-select @error('discussion_id') is-invalid @enderror" id="discussion_id" name="discussion_id" required>
                                        <option value="">Sélectionner une discussion</option>
                                        @foreach($discussions as $discussion)
                                            <option value="{{ $discussion->id }}" {{ old('discussion_id') == $discussion->id ? 'selected' : '' }}>
                                                {{ $discussion->product ? $discussion->product->titre : 'N/A' }} - {{ $discussion->student ? $discussion->student->full_name : 'Student Deleted' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('discussion_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="admin_id" class="form-label">Admin <span class="text-danger">*</span></label>
                                <select class="form-select @error('admin_id') is-invalid @enderror" id="admin_id" name="admin_id" required>
                                    <option value="">Sélectionner un admin</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ old('admin_id') == $admin->id ? 'selected' : '' }}>
                                            {{ $admin->full_name }} ({{ $admin->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('admin_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reponse" class="form-label">Réponse <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('reponse') is-invalid @enderror" id="reponse" name="reponse" rows="6" required>{{ old('reponse') }}</textarea>
                        @error('reponse')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" value="1" {{ old('is_approved') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_approved">
                                Approuver automatiquement cette réponse
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.response-discussions.index') }}" class="btn btn-secondary">
                            <i data-feather="arrow-left"></i> Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i data-feather="save"></i> Créer la réponse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
