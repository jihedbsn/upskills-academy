@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-secondary">
                    <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i>
                    Back to Messages
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i data-feather="check-circle" class="me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i data-feather="alert-circle" class="me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i data-feather="alert-triangle" class="me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Message Details -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <i data-feather="mail" class="me-2"></i>
                                    <h4 class="card-title mb-0">Message Details</h4>
                                </div>
                                <div>
                                    @if($contactMessage->is_read)
                                        <span class="badge bg-light text-dark">
                                            <i data-feather="check" style="width: 12px; height: 12px;"></i> Read
                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i data-feather="circle" style="width: 12px; height: 12px;"></i> Unread
                                        </span>
                                    @endif
                                    @if($contactMessage->responded_at)
                                        <span class="badge bg-success ms-1">
                                            <i data-feather="send" style="width: 12px; height: 12px;"></i> Responded
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Sender Info -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center me-3">
                                    <span class="text-white fw-bold">{{ strtoupper(substr($contactMessage->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $contactMessage->name }}</h5>
                                    <a href="mailto:{{ $contactMessage->email }}" class="text-primary">
                                        {{ $contactMessage->email }}
                                    </a>
                                </div>
                            </div>

                            <!-- Subject -->
                            @if($contactMessage->subject)
                                <div class="mb-3">
                                    <label class="text-muted small">Subject</label>
                                    <h5 class="mb-0">{{ $contactMessage->subject }}</h5>
                                </div>
                            @endif

                            <!-- Date -->
                            <div class="mb-4">
                                <label class="text-muted small">Received</label>
                                <p class="mb-0">
                                    {{ $contactMessage->created_at->format('F j, Y \a\t g:i A') }}
                                    <span class="text-muted">({{ $contactMessage->created_at->diffForHumans() }})</span>
                                </p>
                            </div>

                            <!-- Message -->
                            <div class="bg-light p-4 rounded">
                                <label class="text-muted small mb-2 d-block">Message</label>
                                <div class="message-content" style="white-space: pre-wrap;">{{ $contactMessage->message }}</div>
                            </div>

                            <!-- Previous Response -->
                            @if($contactMessage->admin_response)
                                <div class="mt-4 p-4 bg-success bg-opacity-10 rounded border-start border-success border-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <label class="text-success fw-semibold">
                                            <i data-feather="send" style="width: 14px; height: 14px;"></i>
                                            Your Response
                                        </label>
                                        <small class="text-muted">
                                            {{ $contactMessage->responded_at ? $contactMessage->responded_at->format('M j, Y g:i A') : '' }}
                                        </small>
                                    </div>
                                    <div style="white-space: pre-wrap;">{{ $contactMessage->admin_response }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Response Form -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i data-feather="send" class="me-2" style="width: 18px; height: 18px;"></i>
                                {{ $contactMessage->admin_response ? 'Send Another Response' : 'Send Response' }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('contact-messages.respond', $contactMessage) }}" method="POST" id="responseForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="response" class="form-label">Your Response</label>
                                    <textarea name="response" id="response" rows="6" class="form-control @error('response') is-invalid @enderror"
                                              placeholder="Write your response here...">{{ old('response') }}</textarea>
                                    @error('response')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i data-feather="send" style="width: 16px; height: 16px;"></i>
                                        Send Response
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('response').value = '';">
                                        <i data-feather="x" style="width: 16px; height: 16px;"></i>
                                        Clear
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Actions -->
                <div class="col-lg-4">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i data-feather="settings" class="me-2" style="width: 18px; height: 18px;"></i>
                                Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject }}"
                                   class="btn btn-outline-primary">
                                    <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                    Reply via Email Client
                                </a>

                                @if($contactMessage->is_read)
                                    <form action="{{ route('contact-messages.mark-unread', $contactMessage) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary w-100">
                                            <i data-feather="mail" class="me-2" style="width: 16px; height: 16px;"></i>
                                            Mark as Unread
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('contact-messages.mark-read', $contactMessage) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success w-100">
                                            <i data-feather="check" class="me-2" style="width: 16px; height: 16px;"></i>
                                            Mark as Read
                                        </button>
                                    </form>
                                @endif

                                <hr class="my-2">

                                <form action="{{ route('contact-messages.destroy', $contactMessage) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i data-feather="trash-2" class="me-2" style="width: 16px; height: 16px;"></i>
                                        Delete Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Message Info -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i data-feather="info" class="me-2" style="width: 18px; height: 18px;"></i>
                                Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">ID</td>
                                    <td>#{{ $contactMessage->id }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Received</td>
                                    <td>{{ $contactMessage->created_at->format('M j, Y') }}</td>
                                </tr>
                                @if($contactMessage->read_at)
                                <tr>
                                    <td class="text-muted">Read At</td>
                                    <td>{{ $contactMessage->read_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                @endif
                                @if($contactMessage->responded_at)
                                <tr>
                                    <td class="text-muted">Responded At</td>
                                    <td>{{ $contactMessage->responded_at->format('M j, Y g:i A') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
@endsection
