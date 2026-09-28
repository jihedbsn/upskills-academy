@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-envelope-open"></i> Message Details</h2>
                <a href="{{ route('internal-messages.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Messages
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ $message->subject }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-user"></i> Sent by: Admin
                            <span class="mx-2">|</span>
                            <i class="fas fa-clock"></i> {{ $message->created_at->format('F d, Y \a\t H:i') }}
                        </small>
                    </div>

                    <hr>

                    <div class="message-body">
                        {!! nl2br(e($message->body)) !!}
                    </div>

                    @if($message->attachments && count($message->attachments) > 0)
                        <hr>
                        <div class="attachments">
                            <h5><i class="fas fa-paperclip"></i> Attachments</h5>
                            <div class="list-group">
                                @foreach($message->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment['path']) }}"
                                       class="list-group-item list-group-item-action"
                                       target="_blank"
                                       download="{{ $attachment['name'] }}">
                                        <i class="fas fa-file"></i> {{ $attachment['name'] }}
                                        <small class="text-muted">({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Student Responses Section -->
            @php
                $responsesByStudent = \App\Models\MessageResponse::where('message_id', $message->id)
                    ->with(['student', 'adminResponses.admin'])
                    ->orderBy('student_id')
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy('student_id');
            @endphp

            @if($responsesByStudent->count() > 0)
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Student Responses by Student
                        <span class="badge bg-light text-dark float-end">{{ $responsesByStudent->count() }} Students</span>
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($responsesByStudent as $studentId => $studentResponses)
                        @php
                            $student = $studentResponses->first()->student;
                        @endphp
                        <div class="student-conversation-section mb-5 pb-4 border-bottom">
                            <!-- Student Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded">
                                <div>
                                    <h5 class="mb-1 text-primary">
                                        <i class="fas fa-user-graduate"></i>
                                        {{ $student->first_name }} {{ $student->last_name }}
                                    </h5>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope"></i> {{ $student->email }}
                                    </small>
                                </div>
                                <span class="badge bg-info">{{ $studentResponses->count() }} Response(s)</span>
                            </div>

                            <!-- Student's Responses and Admin Replies -->
                            @foreach($studentResponses as $response)
                                <!-- Student Response -->
                                <div class="mb-3">
                                    <div class="card border-primary {{ !$response->is_read_by_admin ? 'border-danger shadow-sm' : '' }}">
                                        <div class="card-header {{ !$response->is_read_by_admin ? 'bg-danger bg-opacity-10' : 'bg-primary bg-opacity-10' }}">
                                            <small class="text-muted">
                                                @if(!$response->is_read_by_admin)
                                                    <span class="badge bg-danger me-1">New</span>
                                                @endif
                                                <i class="fas fa-user"></i> <strong>Student Response</strong>
                                                <span class="float-end">
                                                    <i class="far fa-clock"></i>
                                                    {{ $response->created_at->format('F d, Y \a\t H:i') }}
                                                </span>
                                            </small>
                                        </div>
                                        <div class="card-body">
                                            <div class="response-body" style="white-space: pre-wrap;">
                                                {{ $response->response_body }}
                                            </div>

                                            @if($response->response_attachments && count($response->response_attachments) > 0)
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-paperclip"></i> Attachments:
                                                    </small>
                                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                                        @foreach($response->response_attachments as $attachment)
                                                            <a href="{{ env('USER_URL') . '/storage/' . $attachment['path'] }}"
                                                               class="btn btn-sm btn-outline-secondary"
                                                               target="_blank"
                                                               download="{{ $attachment['name'] }}">
                                                                <i class="fas fa-file"></i> {{ $attachment['name'] }}
                                                                <small>({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Admin Responses to this student response -->
                                    @if($response->adminResponses->count() > 0)
                                        <div class="ms-5 mt-2">
                                            @foreach($response->adminResponses as $adminResponse)
                                                <div class="card border-success mb-2">
                                                    <div class="card-header bg-success bg-opacity-10">
                                                        <small class="text-muted">
                                                            <i class="fas fa-user-shield"></i> <strong>Admin Reply by {{ $adminResponse->admin->name ?? 'Unknown Admin' }}</strong>
                                                            <span class="float-end">
                                                                <i class="far fa-clock"></i>
                                                                {{ $adminResponse->created_at->format('F d, Y \a\t H:i') }}
                                                            </span>
                                                        </small>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="response-body" style="white-space: pre-wrap;">
                                                            {{ $adminResponse->response_body }}
                                                        </div>

                                                        @if($adminResponse->response_attachments && count($adminResponse->response_attachments) > 0)
                                                            <div class="mt-3">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-paperclip"></i> Attachments:
                                                                </small>
                                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                                    @foreach($adminResponse->response_attachments as $attachment)
                                                                        <a href="{{ asset('storage/' . $attachment['path']) }}"
                                                                           class="btn btn-sm btn-outline-secondary"
                                                                           target="_blank"
                                                                           download="{{ $attachment['name'] }}">
                                                                            <i class="fas fa-file"></i> {{ $attachment['name'] }}
                                                                            <small>({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                                        </a>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Admin Reply Form -->
                                    <div class="ms-5 mt-3">
                                        <div class="card border-secondary">
                                            <div class="card-header bg-secondary text-white">
                                                <i class="fas fa-reply"></i> Reply to this response
                                            </div>
                                            <div class="card-body">
                                                <form action="{{ route('internal-messages.respond', $response->id) }}"
                                                      method="POST"
                                                      enctype="multipart/form-data">
                                                    @csrf

                                                    <div class="mb-3">
                                                        <label for="response_body_{{ $response->id }}" class="form-label">Your Response</label>
                                                        <textarea name="response_body"
                                                                  id="response_body_{{ $response->id }}"
                                                                  class="form-control @error('response_body') is-invalid @enderror"
                                                                  rows="4"
                                                                  placeholder="Type your response to this student..."
                                                                  required></textarea>
                                                        @error('response_body')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="response_attachments_{{ $response->id }}" class="form-label">
                                                            <i class="fas fa-paperclip"></i> Attach Files (Optional)
                                                        </label>
                                                        <input type="file"
                                                               name="response_attachments[]"
                                                               id="response_attachments_{{ $response->id }}"
                                                               class="form-control"
                                                               multiple
                                                               accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip">
                                                        <small class="form-text text-muted">Maximum file size: 10MB per file</small>
                                                    </div>

                                                    <div class="d-grid">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-paper-plane"></i> Send Reply
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No student responses yet.
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users"></i> Recipients
                        <span class="badge bg-light text-dark float-end">{{ $recipients->count() }}</span>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="mb-3 p-3 bg-light">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-success mb-0">{{ $recipients->where('is_read', 1)->count() }}</h3>
                                <small class="text-muted">Read</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-warning mb-0">{{ $recipients->where('is_read', 0)->count() }}</h3>
                                <small class="text-muted">Unread</small>
                            </div>
                        </div>
                    </div>

                    <div class="recipient-list" style="max-height: 500px; overflow-y: auto;">
                        @foreach($recipients as $recipient)
                            <div class="px-3 py-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $recipient->student->first_name }} {{ $recipient->student->last_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $recipient->student->email }}</small>
                                    </div>
                                    <div>
                                        @if($recipient->is_read)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-double"></i> Read
                                            </span>
                                            <br>
                                            <small class="text-muted">
                                                {{ $recipient->read_at->format('M d, H:i') }}
                                            </small>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-envelope"></i> Unread
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
