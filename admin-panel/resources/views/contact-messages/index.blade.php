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
                                <div class="avatar avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i data-feather="mail" class="text-white" style="width: 24px; height: 24px;"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="page-title">Contact Messages</h1>
                                <p class="text-muted mb-0">Messages from website visitors</p>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($unreadCount > 0)
                                <span class="badge bg-danger">{{ $unreadCount }} unread</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
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

                    <!-- Search and Filter Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('contact-messages.index') }}" class="d-flex gap-2">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i data-feather="search" style="width: 16px; height: 16px;"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Search messages..."
                                           value="{{ request('search') }}">
                                </div>
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                                    <option value="responded" {{ request('status') == 'responded' ? 'selected' : '' }}>Responded</option>
                                </select>
                                <button class="btn btn-primary" type="submit">
                                    <i data-feather="filter" style="width: 16px; height: 16px;"></i>
                                </button>
                                @if(request('search') || request('status'))
                                    <a href="{{ route('contact-messages.index') }}" class="btn btn-outline-secondary">
                                        <i data-feather="x" style="width: 16px; height: 16px;"></i>
                                    </a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <span class="text-muted">
                                <i data-feather="inbox" class="me-1" style="width: 16px; height: 16px;"></i>
                                Total: {{ $messages->total() }} messages
                            </span>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="mb-3" id="bulkActionsBar" style="display: none;">
                        <div class="d-flex align-items-center gap-2 bg-light p-2 rounded">
                            <span class="text-muted" id="selectedCount">0 selected</span>
                            <button type="button" class="btn btn-sm btn-success" onclick="bulkMarkAsRead()">
                                <i data-feather="check" style="width: 14px; height: 14px;"></i> Mark as Read
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                <i data-feather="trash-2" style="width: 14px; height: 14px;"></i> Delete
                            </button>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input me-3" id="selectAll" onchange="toggleSelectAll()">
                                <i data-feather="inbox" class="me-2"></i>
                                <h4 class="card-title mb-0">Messages</h4>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($messages->count() > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($messages as $message)
                                        <div class="list-group-item list-group-item-action {{ !$message->is_read ? 'bg-light-info' : '' }}"
                                             style="{{ !$message->is_read ? 'border-left: 4px solid #0dcaf0;' : '' }}">
                                            <div class="d-flex align-items-start">
                                                <input type="checkbox" class="form-check-input me-3 mt-1 message-checkbox"
                                                       value="{{ $message->id }}" onchange="updateBulkActions()">
                                                <div class="flex-grow-1">
                                                    <a href="{{ route('contact-messages.show', $message) }}" class="text-decoration-none">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <div>
                                                                <h6 class="mb-0 {{ !$message->is_read ? 'fw-bold' : '' }}">
                                                                    {{ $message->name }}
                                                                    @if(!$message->is_read)
                                                                        <span class="badge bg-info ms-2">New</span>
                                                                    @endif
                                                                    @if($message->responded_at)
                                                                        <span class="badge bg-success ms-1">Responded</span>
                                                                    @endif
                                                                </h6>
                                                                <small class="text-muted">{{ $message->email }}</small>
                                                            </div>
                                                            <small class="text-muted">
                                                                {{ $message->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                        @if($message->subject)
                                                            <p class="mb-1 {{ !$message->is_read ? 'fw-semibold' : '' }} text-dark">
                                                                {{ $message->subject }}
                                                            </p>
                                                        @endif
                                                        <p class="mb-0 text-muted text-truncate" style="max-width: 600px;">
                                                            {{ Str::limit($message->message, 100) }}
                                                        </p>
                                                    </a>
                                                </div>
                                                <div class="ms-3">
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                                type="button" data-bs-toggle="dropdown">
                                                            <i data-feather="more-vertical" style="width: 14px; height: 14px;"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('contact-messages.show', $message) }}">
                                                                    <i data-feather="eye" class="me-2" style="width: 14px; height: 14px;"></i>
                                                                    View
                                                                </a>
                                                            </li>
                                                            @if($message->is_read)
                                                                <li>
                                                                    <a class="dropdown-item" href="#"
                                                                       onclick="markAsUnread({{ $message->id }}); return false;">
                                                                        <i data-feather="mail" class="me-2" style="width: 14px; height: 14px;"></i>
                                                                        Mark as Unread
                                                                    </a>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <a class="dropdown-item" href="#"
                                                                       onclick="markAsRead({{ $message->id }}); return false;">
                                                                        <i data-feather="check" class="me-2" style="width: 14px; height: 14px;"></i>
                                                                        Mark as Read
                                                                    </a>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#"
                                                                   onclick="deleteMessage({{ $message->id }}); return false;">
                                                                    <i data-feather="trash-2" class="me-2" style="width: 14px; height: 14px;"></i>
                                                                    Delete
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i data-feather="inbox" style="width: 48px; height: 48px;" class="text-muted mb-3"></i>
                                    <h5 class="text-muted">No messages found</h5>
                                    <p class="text-muted">Contact messages from website visitors will appear here.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pagination -->
                    @if($messages->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $messages->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-info {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>

@push('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.message-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.message-checkbox:checked');
        const bulkBar = document.getElementById('bulkActionsBar');
        const countEl = document.getElementById('selectedCount');

        if (checkboxes.length > 0) {
            bulkBar.style.display = 'block';
            countEl.textContent = checkboxes.length + ' selected';
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.message-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function bulkMarkAsRead() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        fetch('{{ route("contact-messages.bulk-mark-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        });
    }

    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        if (!confirm('Are you sure you want to delete ' + ids.length + ' message(s)?')) return;

        fetch('{{ route("contact-messages.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ids: ids })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error occurred');
            }
        });
    }

    function markAsRead(id) {
        fetch('/contact-messages/' + id + '/mark-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function markAsUnread(id) {
        fetch('/contact-messages/' + id + '/mark-unread', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    function deleteMessage(id) {
        if (!confirm('Are you sure you want to delete this message?')) return;

        fetch('/contact-messages/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }

    // Initialize feather icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
@endsection
