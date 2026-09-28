@extends('student-dashboard.layouts.app')

@section('title', __('messages.inbox'))

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0"><i class="fas fa-inbox me-2"></i> {{ __('messages.inbox') }}</h1>
            <p class="text-muted mb-0">{{ __('messages.view_and_manage_messages') }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">
                            {{ __('messages.all_messages') }}
                            @if($unreadCount > 0)
                                <span class="badge bg-primary" id="studentMessagesUnreadBadge">{{ $unreadCount }}</span>
                            @endif
                        </h4>
                    </div>

                    @if($messages->count() > 0)
                        <div class="list-group" id="studentMessagesList">
                            @foreach($messages as $message)
                                @php
                                    $isUnread = $message->is_unread ?? (!$message->is_read || ($message->unread_admin_replies ?? 0) > 0);
                                @endphp
                                <a href="{{ route('student.messages.show', ['locale' => app()->getLocale(), 'id' => $message->id]) }}"
                                   class="list-group-item list-group-item-action student-message-item {{ $isUnread ? 'student-message-unread' : '' }}"
                                   data-message-id="{{ $message->id }}"
                                   data-is-unread="{{ $isUnread ? '1' : '0' }}">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1 gap-2">
                                                @if($isUnread)
                                                    <span class="student-message-unread-dot" aria-hidden="true"></span>
                                                    <span class="badge bg-primary">{{ __('messages.new_message') }}</span>
                                                @endif
                                                <h5 class="mb-0 {{ $isUnread ? 'fw-bold' : 'fw-normal' }}">
                                                    {{ $message->subject }}
                                                </h5>
                                            </div>
                                            <p class="mb-1 text-muted small {{ $isUnread ? 'fw-semibold' : '' }}">
                                                {{ __('messages.message_from_admin') }}
                                                @if(($message->unread_admin_replies ?? 0) > 0)
                                                    · {{ $message->unread_admin_replies }} {{ __('messages.new_message') }}
                                                @endif
                                            </p>
                                            <p class="mb-1 text-muted small">
                                                {{ Str::limit(strip_tags($message->body), 100) }}
                                            </p>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i>
                                                    {{ \Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                                                </small>
                                                @if($message->attachments)
                                                    @php
                                                        $attachments = json_decode($message->attachments, true);
                                                    @endphp
                                                    @if($attachments && count($attachments) > 0)
                                                        <span class="ms-3">
                                                            <i class="fas fa-paperclip text-muted"></i>
                                                            <small class="text-muted">{{ count($attachments) }}</small>
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <i class="fas fa-chevron-right text-muted"></i>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h4>{{ __('messages.no_messages') }}</h4>
                            <p class="text-muted">{{ __('messages.no_messages_description') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.student-message-unread {
    background: linear-gradient(90deg, rgba(13, 110, 253, 0.1), rgba(13, 110, 253, 0.03));
    border-left: 4px solid #0d6efd !important;
}

.student-message-unread-dot {
    display: inline-block;
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #0d6efd;
    flex-shrink: 0;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const locale = @json(app()->getLocale());
    const markReadUrlTemplate = @json(url('/')) + locale + '/student-dashboard/messages/__ID__/mark-read';
    const syncUrl = @json(route('student.messages.sync-status', ['locale' => app()->getLocale()]));
    const csrfToken = @json(csrf_token());

    function applyStudentMessageState(messageId, isUnread) {
        const item = document.querySelector(`.student-message-item[data-message-id="${messageId}"]`);
        if (!item) {
            return;
        }

        item.dataset.isUnread = isUnread ? '1' : '0';
        item.classList.toggle('student-message-unread', isUnread);

        const title = item.querySelector('h5');
        if (title) {
            title.classList.toggle('fw-bold', isUnread);
            title.classList.toggle('fw-normal', !isUnread);
        }
    }

    function updateUnreadBadge(count) {
        let badge = document.getElementById('studentMessagesUnreadBadge');
        if (count > 0) {
            if (!badge) {
                const heading = document.querySelector('.card-body h4');
                if (!heading) return;
                badge = document.createElement('span');
                badge.id = 'studentMessagesUnreadBadge';
                badge.className = 'badge bg-primary';
                heading.appendChild(document.createTextNode(' '));
                heading.appendChild(badge);
            }
            badge.textContent = count;
        } else if (badge) {
            badge.remove();
        }
    }

    document.querySelectorAll('.student-message-item').forEach(item => {
        item.addEventListener('click', function () {
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
            }).then(response => response.json())
              .then(payload => {
                  applyStudentMessageState(messageId, false);
                  if (payload.unread_count !== undefined) {
                      updateUnreadBadge(payload.unread_count);
                  }
              }).catch(() => {});
        });
    });

    function syncStudentMessages() {
        fetch(syncUrl, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        }).then(response => response.json())
          .then(payload => {
              if (!payload.success) {
                  return;
              }

              updateUnreadBadge(payload.unread_count || 0);
              (payload.messages || []).forEach(row => {
                  applyStudentMessageState(row.id, !!row.is_unread);
              });
          }).catch(() => {});
    }

    syncStudentMessages();
    setInterval(syncStudentMessages, 15000);
});
</script>
@endpush
@endsection
