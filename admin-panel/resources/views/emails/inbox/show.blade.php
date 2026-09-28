@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">Conversation</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('emails.inbox.index') }}">Inbox</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($thread->subject, 40) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    @include('emails._tabs')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ $thread->subject }}</h4>
                        <small class="text-muted">
                            <i class="fa fa-user me-1"></i>{{ $thread->participant_name ?: $thread->participant_email }}
                            · {{ $thread->participant_email }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        @if($thread->status === 'open')
                            <form method="POST" action="{{ route('emails.inbox.close', $thread) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm">Close</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('emails.inbox.reopen', $thread) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-success btn-sm">Reopen</button>
                            </form>
                        @endif
                        <a href="{{ route('emails.inbox.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body thread-messages">
                    @foreach($thread->messages as $message)
                        <div class="thread-message mb-4 pb-3 border-bottom {{ $message->isInbound() ? 'inbound-message' : 'outbound-message' }}">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    @if($message->isInbound())
                                        <span class="badge bg-info me-2">Received</span>
                                        <strong>{{ $message->from_name ?: $message->from_email }}</strong>
                                    @else
                                        <span class="badge bg-primary me-2">Sent</span>
                                        <strong>{{ $message->from_name ?: 'Admin' }}</strong>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $message->created_at->format('M d, Y H:i') }}</small>
                                </div>
                            </div>

                            <div class="message-body">
                                @if($message->body_html)
                                    <div class="border rounded p-3 bg-light">{!! $message->body_html !!}</div>
                                @else
                                    {!! nl2br(e($message->body)) !!}
                                @endif
                            </div>

                            @if($message->attachments->count() > 0)
                                <div class="mt-3">
                                    <strong><i class="fa fa-paperclip"></i> Attachments</strong>
                                    <div class="list-group mt-2">
                                        @foreach($message->attachments as $attachment)
                                            <a href="{{ asset('storage/' . $attachment->path) }}"
                                               class="list-group-item list-group-item-action"
                                               target="_blank" download="{{ $attachment->name }}">
                                                {{ $attachment->name }}
                                                <small class="text-muted">({{ number_format($attachment->size / 1024, 1) }} KB)</small>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa fa-reply me-2"></i>Reply</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('emails.inbox.reply', $thread) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">To</label>
                            <input type="email" class="form-control" value="{{ $thread->participant_email }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" value="{{ $thread->replySubject() }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea name="content" class="form-control" rows="8" required placeholder="Write your reply...">{{ old('content') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <input type="file" name="attachments[]" class="form-control" multiple
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-paper-plane me-1"></i> Send Reply
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.inbound-message { border-left: 3px solid #17a2b8; padding-left: 12px; }
.outbound-message { border-left: 3px solid #093162; padding-left: 12px; }
</style>
@endsection
