@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="avatar avatar-lg bg-primary rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="message-circle" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Chat Conversations</h1>
                                <p class="text-muted mb-0">Manage chatbot conversations and take over when needed</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                                    <small class="text-muted">Total Conversations</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white border-0">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['active'] }}</h3>
                                    <small>Active (AI)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white border-0">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['admin_taken'] }}</h3>
                                    <small>Admin Handling</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark border-0">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $stats['unread'] }}</h3>
                                    <small>Unread Messages</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('admin.chat.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                        </span>
                                        <input type="text" name="search" class="form-control"
                                               placeholder="Search by name, email, IP..."
                                               value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-select">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active (AI)</option>
                                        <option value="admin_taken" {{ request('status') == 'admin_taken' ? 'selected' : '' }}>Admin Taken</option>
                                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check form-switch mt-2">
                                        <input class="form-check-input" type="checkbox" name="unread" value="1" id="unreadOnly"
                                               {{ request('unread') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="unreadOnly">Unread Only</label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary w-100" type="submit">
                                        <i data-feather="filter" style="width: 16px; height: 16px;" class="me-1"></i> Filter
                                    </button>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary w-100">
                                        <i data-feather="x" style="width: 16px; height: 16px;" class="me-1"></i> Clear
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Conversations List -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Visitor</th>
                                    <th>Last Message</th>
                                    <th>Status</th>
                                    <th>Unread</th>
                                    <th>Language</th>
                                    <th>Last Activity</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($conversations as $conversation)
                                <tr class="{{ $conversation->unread_admin_count > 0 ? 'table-warning' : '' }}">
                                    <td>{{ $conversation->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @php
                                                $isMessenger = str_starts_with($conversation->session_id, 'messenger_');
                                                $isInstagram = str_starts_with($conversation->session_id, 'instagram_');
                                                $isWhatsApp = str_starts_with($conversation->session_id, 'whatsapp_');
                                                $avatarBg = $conversation->student ? 'primary' : ($isMessenger ? 'info' : ($isInstagram ? 'danger' : ($isWhatsApp ? 'success' : 'secondary')));
                                                $avatarIcon = $isMessenger ? 'message-circle' : ($isInstagram ? 'instagram' : ($isWhatsApp ? 'phone' : ($conversation->student ? 'user' : 'user-x')));
                                            @endphp
                                            <div class="avatar avatar-sm bg-{{ $avatarBg }} rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                <i data-feather="{{ $avatarIcon }}" class="text-white" style="width: 14px; height: 14px;"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $conversation->display_name }}</strong>
                                                @if($isMessenger)
                                                    <span class="badge bg-info ms-1" style="font-size: 9px;">Messenger</span>
                                                @elseif($isInstagram)
                                                    <span class="badge bg-danger ms-1" style="font-size: 9px;">Instagram</span>
                                                @elseif($isWhatsApp)
                                                    <span class="badge bg-success ms-1" style="font-size: 9px;">WhatsApp</span>
                                                @endif
                                                @if($conversation->student)
                                                <br><small class="text-muted">{{ $conversation->student->email }}</small>
                                                @elseif($isMessenger || $isInstagram || $isWhatsApp)
                                                <br><small class="text-muted">Social Media</small>
                                                @else
                                                <br><small class="text-muted">{{ $conversation->visitor_ip }} - {{ $conversation->visitor_country }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                            {{ $conversation->last_message ?? 'No messages' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $conversation->status_color }}">
                                            @if($conversation->admin_takeover)
                                                <i data-feather="user-check" style="width: 12px; height: 12px;" class="me-1"></i>
                                                Admin: {{ $conversation->admin?->name ?? 'Unknown' }}
                                            @else
                                                <i data-feather="cpu" style="width: 12px; height: 12px;" class="me-1"></i>
                                                {{ ucfirst(str_replace('_', ' ', $conversation->status)) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($conversation->unread_admin_count > 0)
                                        <span class="badge bg-danger">{{ $conversation->unread_admin_count }}</span>
                                        @else
                                        <span class="text-muted">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ strtoupper($conversation->visitor_language) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $conversation->last_message_at?->diffForHumans() ?? 'Never' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.chat.show', $conversation) }}"
                                               class="btn btn-primary" title="View Conversation">
                                                <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            @if(!$conversation->admin_takeover)
                                            <button type="button" class="btn btn-success take-over-btn"
                                                    data-id="{{ $conversation->id }}" title="Take Over">
                                                <i data-feather="log-in" style="width: 14px; height: 14px;"></i>
                                            </button>
                                            @endif
                                            <button type="button" class="btn btn-outline-danger delete-btn"
                                                    data-id="{{ $conversation->id }}" title="Delete">
                                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i data-feather="message-circle" style="width: 48px; height: 48px;" class="mb-3"></i>
                                            <p>No conversations found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $conversations->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Take over conversation
    document.querySelectorAll('.take-over-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Are you sure you want to take over this conversation?')) {
                fetch(`/admin/chat/${id}/take-over`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = `/admin/chat/${id}`;
                    }
                });
            }
        });
    });

    // Delete conversation
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (confirm('Are you sure you want to delete this conversation?')) {
                fetch(`/admin/chat/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    window.location.reload();
                });
            }
        });
    });

    // Auto-refresh every 30 seconds
    setInterval(() => {
        window.location.reload();
    }, 30000);
});
</script>
@endpush
@endsection
