@extends('student-dashboard.layouts.app')

@section('title', $message->subject)

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('student.messages.inbox', ['locale' => app()->getLocale()]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                {{ __('messages.back_to_inbox') }}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h3 class="mb-0">{{ $message->subject }}</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="mb-1">
                                <i class="fas fa-user-shield text-primary"></i>
                                <strong>{{ __('messages.message_from_admin') }}</strong>
                            </p>
                            <p class="mb-0 text-muted">
                                <i class="far fa-clock"></i>
                                {{ __('messages.sent_on') }} {{ \Carbon\Carbon::parse($message->created_at)->format('F d, Y \a\t H:i') }}
                            </p>
                        </div>
                        <div>
                            @if($message->is_read)
                                <form action="{{ route('student.messages.mark-unread', ['locale' => app()->getLocale(), 'id' => $message->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <i class="far fa-envelope"></i> {{ __('messages.mark_as_unread') }}
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-primary">{{ __('messages.new_message') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="message-content" style="line-height: 1.8; white-space: pre-wrap;">
                        {{ $message->body }}
                    </div>

                    @if($message->attachments && count($message->attachments) > 0)
                        <hr class="my-4">
                        <div class="attachments">
                            <h5>
                                <i class="fas fa-paperclip"></i>
                                {{ __('messages.attachments') }} ({{ count($message->attachments) }})
                            </h5>
                            <div class="list-group mt-3">
                                @foreach($message->attachments as $attachment)
                                    @php
                                        $filename = basename($attachment['path']);
                                    @endphp
                                    <a href="{{ env('ADMIN_APP_URL') . '/internal-messages/download/' . $filename }}"
                                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                       target="_blank"
                                       download="{{ $attachment['name'] }}">
                                        <div>
                                            <i class="fas fa-file text-primary"></i>
                                            <strong>{{ $attachment['name'] }}</strong>
                                        </div>
                                        <div>
                                            <span class="badge bg-secondary">
                                                {{ number_format($attachment['size'] / 1024, 2) }} KB
                                            </span>
                                            <i class="fas fa-download ms-2"></i>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light text-muted">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        This message was sent by the administration team
                    </small>
                </div>
            </div>

            <!-- Response Form -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-reply"></i>
                        {{ __('messages.send_response') }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.messages.respond', ['locale' => app()->getLocale(), 'id' => $message->id]) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="response_body" class="form-label">
                                <strong>{{ __('messages.your_response') }}</strong>
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="response_body"
                                      id="response_body"
                                      class="form-control @error('response_body') is-invalid @enderror"
                                      rows="6"
                                      placeholder="{{ __('messages.type_your_response_here') }}"
                                      required>{{ old('response_body') }}</textarea>
                            @error('response_body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="response_attachments" class="form-label">
                                <i class="fas fa-paperclip"></i>
                                {{ __('messages.attach_files') }} ({{ __('messages.optional') }})
                            </label>
                            <input type="file"
                                   name="response_attachments[]"
                                   id="response_attachments"
                                   class="form-control @error('response_attachments.*') is-invalid @enderror"
                                   multiple
                                   accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip">
                            <small class="form-text text-muted">
                                {{ __('messages.max_file_size_10mb') }}
                            </small>
                            @error('response_attachments.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane"></i>
                                {{ __('messages.send_response') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Display Previous Responses -->
            @if($responses->count() > 0)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history"></i>
                        {{ __('messages.your_previous_responses') }} ({{ $responses->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($responses as $response)
                        <div class="response-item p-3 mb-4 border rounded">
                            <!-- Student Response -->
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-primary">
                                        <i class="fas fa-user"></i> {{ __('messages.your_response') }}
                                    </strong>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($response->created_at)->format('F d, Y \a\t H:i') }}
                                    </small>
                                </div>
                                <div class="response-body" style="white-space: pre-wrap;">
                                    {{ $response->response_body }}
                                </div>
                                @if($response->response_attachments)
                                    @php
                                        $attachments = is_string($response->response_attachments)
                                            ? json_decode($response->response_attachments, true)
                                            : (is_array($response->response_attachments) ? $response->response_attachments : []);
                                    @endphp
                                    @if(is_array($attachments) && count($attachments) > 0)
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-paperclip"></i> {{ __('messages.attachments') }}:
                                        </small>
                                        @foreach($attachments as $attachment)
                                            <a href="{{ env('USER_URL') . '/storage/' . $attachment['path'] }}"
                                               class="badge bg-secondary text-decoration-none ms-1"
                                               target="_blank"
                                               download="{{ $attachment['name'] }}">
                                                <i class="fas fa-download"></i> {{ $attachment['name'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Admin Responses to this student response -->
                            @if($response->admin_responses && count($response->admin_responses) > 0)
                                <div class="ms-4">
                                    <h6 class="text-success mb-3">
                                        <i class="fas fa-reply"></i> {{ __('messages.admin_replies') }} ({{ count($response->admin_responses) }})
                                    </h6>
                                    @foreach($response->admin_responses as $adminResponse)
                                        <div class="card border-success mb-3">
                                            <div class="card-header bg-success bg-opacity-10">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small>
                                                        <i class="fas fa-user-shield"></i> <strong>{{ $adminResponse->admin_name }}</strong>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="far fa-clock"></i>
                                                        {{ \Carbon\Carbon::parse($adminResponse->created_at)->format('F d, Y \a\t H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="response-body" style="white-space: pre-wrap;">
                                                    {{ $adminResponse->response_body }}
                                                </div>

                                                @if($adminResponse->response_attachments)
                                                    @php
                                                        $adminAttachments = is_string($adminResponse->response_attachments)
                                                            ? json_decode($adminResponse->response_attachments, true)
                                                            : (is_array($adminResponse->response_attachments) ? $adminResponse->response_attachments : []);
                                                    @endphp
                                                    @if(is_array($adminAttachments) && count($adminAttachments) > 0)
                                                    <div class="mt-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-paperclip"></i> {{ __('messages.attachments') }}:
                                                        </small>
                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            @foreach($adminAttachments as $attachment)
                                                                <a href="{{ env('ADMIN_APP_URL') . '/storage/' . $attachment['path'] }}"
                                                                   class="btn btn-sm btn-outline-secondary"
                                                                   target="_blank"
                                                                   download="{{ $attachment['name'] }}">
                                                                    <i class="fas fa-download"></i> {{ $attachment['name'] }}
                                                                    <small>({{ number_format($attachment['size'] / 1024, 2) }} KB)</small>
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
