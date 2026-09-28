@extends('layouts.app')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">Email Inbox</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Accueil</a></li>
                        <li class="breadcrumb-item active">Inbox</li>
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

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h4 class="card-title mb-0">
                    <i class="fa fa-inbox me-2"></i>Conversations
                    @if($unreadCount > 0)
                        <span class="badge bg-danger">{{ $unreadCount }} unread</span>
                    @endif
                </h4>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('emails.inbox.sync') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-refresh me-1"></i> Sync IMAP
                        </button>
                    </form>
                    <a href="{{ route('emails.index') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-paper-plane me-1"></i> New Email
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search subject, email, name..."
                           value="{{ $search }}">
                </div>
                <div class="col-md-3">
                    <select name="filter" class="form-select">
                        <option value="open" {{ $filter === 'open' ? 'selected' : '' }}>Open</option>
                        <option value="unread" {{ $filter === 'unread' ? 'selected' : '' }}>Unread</option>
                        <option value="closed" {{ $filter === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
            </form>

            @if($threads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Contact</th>
                                <th>Subject</th>
                                <th>Last message</th>
                                <th>Messages</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($threads as $thread)
                                <tr class="{{ $thread->unread_count > 0 ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>{{ $thread->participant_name ?: $thread->participant_email }}</strong>
                                        @if($thread->participant_name)
                                            <br><small class="text-muted">{{ $thread->participant_email }}</small>
                                        @endif
                                        @if($thread->student_id)
                                            <br><span class="badge bg-light text-dark">Student #{{ $thread->student_id }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $thread->subject }}
                                        @if($thread->unread_count > 0)
                                            <span class="badge bg-danger ms-1">{{ $thread->unread_count }}</span>
                                        @endif
                                        @if($thread->latestMessage)
                                            <br><small class="text-muted">{{ Str::limit(strip_tags($thread->latestMessage->body), 80) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ optional($thread->last_message_at)->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td><span class="badge bg-primary">{{ $thread->messages_count }}</span></td>
                                    <td>
                                        @if($thread->status === 'closed')
                                            <span class="badge bg-secondary">Closed</span>
                                        @else
                                            <span class="badge bg-success">Open</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('emails.inbox.show', $thread) }}" class="btn btn-sm btn-outline-primary">
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $threads->withQueryString()->links() }}
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-inbox fa-3x mb-3"></i>
                    <p class="mb-0">No conversations yet. Sent emails and inbound replies will appear here.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
