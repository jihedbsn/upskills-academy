@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="fas fa-inbox"></i> Internal Messages</h2>
                    @if(($totalUnreadResponses ?? 0) > 0)
                        <p class="text-muted mb-0">
                            <span class="badge bg-danger" id="internalMessagesUnreadTotal">{{ $totalUnreadResponses }}</span>
                            unread student response(s) awaiting review
                        </p>
                    @endif
                </div>
                <a href="{{ route('internal-messages.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Compose New Message
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            @if($messages->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="internalMessagesTable">
                        <thead>
                            <tr>
                                <th style="width: 36px;"></th>
                                <th>Subject</th>
                                <th>Recipients</th>
                                <th>Student Read Status</th>
                                <th>Student Replies</th>
                                <th>Attachments</th>
                                <th>Sent Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $message)
                                @php
                                    $hasUnreadResponses = ($message->unread_responses_count ?? 0) > 0;
                                    $readCount = $message->total_recipients - $message->unread_count;
                                    $readPercentage = $message->total_recipients > 0
                                        ? round(($readCount / $message->total_recipients) * 100)
                                        : 0;
                                @endphp
                                <tr class="internal-message-row {{ $hasUnreadResponses ? 'internal-message-unread' : '' }}"
                                    data-message-id="{{ $message->id }}"
                                    data-unread-responses="{{ $message->unread_responses_count ?? 0 }}">
                                    <td class="text-center">
                                        @if($hasUnreadResponses)
                                            <span class="internal-message-unread-dot" title="Unread student response(s)"></span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="{{ $hasUnreadResponses ? 'fw-bold' : '' }}">
                                            {{ $message->subject }}
                                        </div>
                                        @if($hasUnreadResponses)
                                            <small class="text-primary fw-semibold">
                                                <i class="fas fa-comment-dots"></i>
                                                {{ $message->unread_responses_count }} new student response(s)
                                            </small>
                                        @endif
                                        @if($message->attachments)
                                            <i class="fas fa-paperclip text-muted ms-1"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $message->total_recipients }} students</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                     style="width: {{ $readPercentage }}%">
                                                    {{ $readPercentage }}%
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                {{ $readCount }}/{{ $message->total_recipients }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if(($message->total_responses_count ?? 0) > 0)
                                            <span class="badge {{ $hasUnreadResponses ? 'bg-danger' : 'bg-info' }}">
                                                {{ $message->total_responses_count }} total
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($message->attachments)
                                            <span class="badge bg-info">{{ count($message->attachments) }} files</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $message->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('internal-messages.show', $message->id) }}"
                                           class="btn btn-sm {{ $hasUnreadResponses ? 'btn-danger' : 'btn-primary' }} internal-message-view-btn"
                                           data-message-id="{{ $message->id }}">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $messages->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4>No Messages Sent Yet</h4>
                    <p class="text-muted">Start communicating with your students by composing a new message.</p>
                    <a href="{{ route('internal-messages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Compose New Message
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.internal-message-unread {
    background-color: rgba(13, 110, 253, 0.08) !important;
}

.internal-message-unread:hover {
    background-color: rgba(13, 110, 253, 0.12) !important;
}

.internal-message-unread-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.2);
    animation: internalMessagePulse 2s infinite;
}

@keyframes internalMessagePulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.75; transform: scale(0.92); }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const summaryUrl = @json(route('internal-messages.unread-summary'));
    const markReadUrlTemplate = @json(route('internal-messages.mark-read', ['id' => '__ID__']));
    const csrfToken = @json(csrf_token());

    function applyUnreadState(messageId, unreadCount) {
        const row = document.querySelector(`.internal-message-row[data-message-id="${messageId}"]`);
        if (!row) {
            return;
        }

        row.dataset.unreadResponses = unreadCount;
        row.classList.toggle('internal-message-unread', unreadCount > 0);

        const dotCell = row.querySelector('td:first-child');
        if (dotCell) {
            dotCell.innerHTML = unreadCount > 0
                ? '<span class="internal-message-unread-dot" title="Unread student response(s)"></span>'
                : '<span class="text-muted">—</span>';
        }

        const subjectDiv = row.querySelector('td:nth-child(2) div');
        if (subjectDiv) {
            subjectDiv.classList.toggle('fw-bold', unreadCount > 0);
        }

        const viewBtn = row.querySelector('.internal-message-view-btn');
        if (viewBtn) {
            viewBtn.classList.toggle('btn-danger', unreadCount > 0);
            viewBtn.classList.toggle('btn-primary', unreadCount === 0);
        }
    }

    function syncUnreadSummary() {
        fetch(summaryUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
            .then(response => response.json())
            .then(payload => {
                if (!payload.success) {
                    return;
                }

                const totalBadge = document.getElementById('internalMessagesUnreadTotal');
                const total = payload.data.total_unread_responses || 0;

                if (totalBadge) {
                    totalBadge.textContent = total;
                    totalBadge.closest('p')?.classList.toggle('d-none', total === 0);
                }

                const unreadById = {};
                (payload.data.messages || []).forEach(item => {
                    unreadById[item.id] = item.unread_responses_count;
                });

                document.querySelectorAll('.internal-message-row').forEach(row => {
                    const id = parseInt(row.dataset.messageId, 10);
                    applyUnreadState(id, unreadById[id] || 0);
                });
            })
            .catch(() => {});
    }

    document.querySelectorAll('.internal-message-view-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const messageId = this.dataset.messageId;
            const markUrl = markReadUrlTemplate.replace('__ID__', messageId);

            fetch(markUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            }).then(() => {
                applyUnreadState(messageId, 0);
                syncUnreadSummary();
            }).catch(() => {});
        });
    });

    syncUnreadSummary();
    setInterval(syncUnreadSummary, 15000);
});
</script>
@endpush
@endsection
