@extends('layouts.app')

@section('content')
<style>
/* Chat Container */
.admin-chat-container {
    height: calc(100vh - 280px);
    min-height: 450px;
    display: flex;
    flex-direction: column;
    background: #fff;
    border-radius: 0 0 8px 8px;
}

/* Messages Area */
.admin-chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    background: linear-gradient(180deg, #f8f9fa 0%, #fff 100%);
}

/* Message Row */
.chat-msg {
    display: flex;
    margin-bottom: 16px;
    align-items: flex-start;
}

.chat-msg.msg-user {
    justify-content: flex-start;
}

.chat-msg.msg-ai,
.chat-msg.msg-admin {
    justify-content: flex-end;
}

/* Message Bubble */
.chat-bubble {
    max-width: 65%;
    padding: 14px 18px;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* User Message - Left side, blue */
.chat-msg.msg-user .chat-bubble {
    background: #e8f4fd;
    color: #1565c0;
    border-bottom-left-radius: 6px;
}

/* AI Bot Message - Right side, gray */
.chat-msg.msg-ai .chat-bubble {
    background: #f1f3f4;
    color: #3c4043;
    border-bottom-right-radius: 6px;
}

/* Admin Message - Right side, purple gradient */
.chat-msg.msg-admin .chat-bubble {
    background: linear-gradient(135deg, #7367f0 0%, #9e95f5 100%);
    color: #fff;
    border-bottom-right-radius: 6px;
}

/* Sender Label */
.chat-sender {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.chat-msg.msg-user .chat-sender {
    color: #1976d2;
}

.chat-msg.msg-ai .chat-sender {
    color: #5f6368;
    justify-content: flex-end;
}

.chat-msg.msg-admin .chat-sender {
    color: rgba(255,255,255,0.9);
    justify-content: flex-end;
}

/* Message Content */
.chat-text {
    font-size: 14px;
    line-height: 1.6;
    word-wrap: break-word;
}

.chat-msg.msg-ai .chat-text,
.chat-msg.msg-admin .chat-text {
    text-align: right;
}

/* Message Time */
.chat-time {
    font-size: 10px;
    margin-top: 8px;
    opacity: 0.7;
}

.chat-msg.msg-ai .chat-time,
.chat-msg.msg-admin .chat-time {
    text-align: right;
}

/* Input Area */
.admin-chat-input {
    padding: 20px 24px;
    background: #fff;
    border-top: 1px solid #e9ecef;
}

.admin-chat-input .input-group {
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    border-radius: 30px;
    overflow: hidden;
}

.admin-chat-input .form-control {
    border: none;
    padding: 16px 24px;
    font-size: 15px;
}

.admin-chat-input .form-control:focus {
    box-shadow: none;
}

.admin-chat-input .btn-send {
    border: none;
    background: linear-gradient(135deg, #7367f0 0%, #9e95f5 100%);
    color: #fff;
    padding: 12px 24px;
    border-radius: 0 30px 30px 0 !important;
}

.admin-chat-input .btn-send:hover {
    background: linear-gradient(135deg, #6355e0 0%, #8e85e5 100%);
}

.admin-chat-input .btn-send:disabled {
    background: #ccc;
}

/* Typing Indicator */
.typing-indicator-bar {
    padding: 8px 24px;
    color: #6c757d;
    font-size: 13px;
    font-style: italic;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>

<div class="container-fluid">
    <div class="row">
        <!-- Main Chat Area -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                            <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i>
                        </a>
                        @php
                            $isMessenger = str_starts_with($conversation->session_id, 'messenger_');
                            $isInstagram = str_starts_with($conversation->session_id, 'instagram_');
                            $isWhatsApp = str_starts_with($conversation->session_id, 'whatsapp_');
                            $avatarBg = $conversation->student ? 'primary' : ($isMessenger ? 'info' : ($isInstagram ? 'danger' : ($isWhatsApp ? 'success' : 'secondary')));
                        @endphp
                        <div class="avatar avatar-md bg-{{ $avatarBg }} rounded-circle me-3 d-flex align-items-center justify-content-center">
                            @if($isMessenger)
                                <i data-feather="message-circle" class="text-white" style="width: 20px; height: 20px;"></i>
                            @elseif($isInstagram)
                                <i data-feather="instagram" class="text-white" style="width: 20px; height: 20px;"></i>
                            @elseif($isWhatsApp)
                                <i data-feather="phone" class="text-white" style="width: 20px; height: 20px;"></i>
                            @else
                                <i data-feather="user" class="text-white" style="width: 20px; height: 20px;"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-0">
                                {{ $conversation->display_name }}
                                @if($isMessenger)
                                    <span class="badge bg-info ms-2" style="font-size: 10px;">Messenger</span>
                                @elseif($isInstagram)
                                    <span class="badge bg-danger ms-2" style="font-size: 10px;">Instagram</span>
                                @elseif($isWhatsApp)
                                    <span class="badge bg-success ms-2" style="font-size: 10px;">WhatsApp</span>
                                @endif
                            </h5>
                            <small class="text-muted">
                                @if($conversation->student)
                                    {{ $conversation->student->email }}
                                @elseif($isMessenger || $isInstagram || $isWhatsApp)
                                    Social Media User
                                @else
                                    {{ $conversation->visitor_ip }} - {{ $conversation->visitor_country }}
                                @endif
                            </small>
                        </div>
                    </div>
                    <div class="action-buttons">
                        @if($conversation->admin_takeover)
                            <span class="badge bg-primary status-badge-large me-2">
                                <i data-feather="user-check" style="width: 14px; height: 14px;" class="me-1"></i>
                                You're handling this chat
                            </span>
                            <button type="button" class="btn btn-outline-warning btn-sm" id="releaseBtn">
                                <i data-feather="log-out" style="width: 14px; height: 14px;"></i> Release to AI
                            </button>
                        @else
                            <span class="badge bg-success status-badge-large me-2">
                                <i data-feather="cpu" style="width: 14px; height: 14px;" class="me-1"></i>
                                AI is responding
                            </span>
                            <button type="button" class="btn btn-success btn-sm" id="takeOverBtn">
                                <i data-feather="log-in" style="width: 14px; height: 14px;"></i> Take Over
                            </button>
                        @endif
                        <button type="button" class="btn btn-outline-danger btn-sm" id="closeConvBtn">
                            <i data-feather="x-circle" style="width: 14px; height: 14px;"></i> Close
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="admin-chat-container">
                        <div class="admin-chat-messages" id="chatMessages">
                            @foreach($conversation->messages as $message)
                            <div class="chat-msg msg-{{ $message->sender_type }}">
                                <div class="chat-bubble">
                                    <div class="chat-sender">
                                        @if($message->sender_type === 'user')
                                            <i data-feather="user" style="width: 12px; height: 12px;"></i> User
                                        @elseif($message->sender_type === 'ai')
                                            <i data-feather="cpu" style="width: 12px; height: 12px;"></i> AI Bot
                                        @else
                                            <i data-feather="shield" style="width: 12px; height: 12px;"></i>
                                            Admin
                                        @endif
                                    </div>
                                    <div class="chat-text">{!! nl2br(e($message->message)) !!}</div>
                                    <div class="chat-time">
                                        {{ $message->created_at->format('M d, H:i') }}
                                        @if($message->is_read)
                                            <i data-feather="check-circle" style="width: 10px; height: 10px;" class="ms-1"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="typing-indicator-bar" id="typingIndicator" style="display: none;">
                            <i data-feather="more-horizontal" style="width: 16px; height: 16px;"></i>
                            User is typing...
                        </div>

                        <div class="admin-chat-input">
                            <form id="chatForm">
                                <div class="input-group">
                                    <input type="text" class="form-control"
                                           id="messageInput"
                                           placeholder="{{ $conversation->admin_takeover ? 'Type your message...' : 'Take over to send messages...' }}"
                                           {{ !$conversation->admin_takeover ? 'disabled' : '' }}>
                                    <button type="submit" class="btn btn-send"
                                            id="sendBtn"
                                            {{ !$conversation->admin_takeover ? 'disabled' : '' }}>
                                        <i data-feather="send" style="width: 18px; height: 18px;"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversation Info Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i data-feather="info" style="width: 16px; height: 16px;" class="me-2"></i>
                        Conversation Details
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Session ID:</td>
                            <td><code>{{ \Str::limit($conversation->session_id, 20) }}</code></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td><span class="badge bg-{{ $conversation->status_color }}">{{ ucfirst(str_replace('_', ' ', $conversation->status)) }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Language:</td>
                            <td>{{ strtoupper($conversation->visitor_language) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">IP Address:</td>
                            <td>{{ $conversation->visitor_ip ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Country:</td>
                            <td>{{ $conversation->visitor_country ?? 'Unknown' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Started:</td>
                            <td>{{ $conversation->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Last Activity:</td>
                            <td>{{ $conversation->last_message_at?->diffForHumans() ?? 'Never' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Messages:</td>
                            <td>{{ $conversation->messages->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($conversation->student)
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i data-feather="user" style="width: 16px; height: 16px;" class="me-2"></i>
                        Student Info
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Name:</td>
                            <td>{{ $conversation->student->first_name }} {{ $conversation->student->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Email:</td>
                            <td>{{ $conversation->student->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Phone:</td>
                            <td>{{ $conversation->student->phone ?? 'N/A' }}</td>
                        </tr>
                    </table>
                    <a href="{{ route('students.show', $conversation->student) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i data-feather="external-link" style="width: 14px; height: 14px;" class="me-1"></i>
                        View Student Profile
                    </a>
                </div>
            </div>
            @endif

            @if($conversation->admin && $conversation->admin_takeover)
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i data-feather="shield" style="width: 16px; height: 16px;" class="me-2"></i>
                        Handled By
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center">
                            <i data-feather="user" class="text-white" style="width: 14px; height: 14px;"></i>
                        </div>
                        <div>
                            <strong>{{ $conversation->admin->name }}</strong>
                            <br><small class="text-muted">Since {{ $conversation->admin_takeover_at?->format('M d, H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const conversationId = {{ $conversation->id }};
    const chatMessages = document.getElementById('chatMessages');
    const messageInput = document.getElementById('messageInput');
    const chatForm = document.getElementById('chatForm');
    const sendBtn = document.getElementById('sendBtn');
    let lastMessageId = {{ $conversation->messages->last()?->id ?? 0 }};
    let isAdminTakeover = {{ $conversation->admin_takeover ? 'true' : 'false' }};

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    scrollToBottom();

    // Add message to chat
    function addMessage(message) {
        const msgClass = `msg-${message.sender_type}`;

        let senderIcon = '';
        let senderLabel = '';
        if (message.sender_type === 'user') {
            senderIcon = '<i data-feather="user" style="width: 12px; height: 12px;"></i>';
            senderLabel = 'User';
        } else if (message.sender_type === 'ai') {
            senderIcon = '<i data-feather="cpu" style="width: 12px; height: 12px;"></i>';
            senderLabel = 'AI Bot';
        } else {
            senderIcon = '<i data-feather="shield" style="width: 12px; height: 12px;"></i>';
            senderLabel = 'Admin';
        }

        const html = `
            <div class="chat-msg ${msgClass}">
                <div class="chat-bubble">
                    <div class="chat-sender">
                        ${senderIcon} ${senderLabel}
                    </div>
                    <div class="chat-text">${message.message.replace(/\n/g, '<br>')}</div>
                    <div class="chat-time">${message.timestamp}</div>
                </div>
            </div>
        `;

        chatMessages.insertAdjacentHTML('beforeend', html);
        feather.replace();
        scrollToBottom();
    }

    // Send message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const message = messageInput.value.trim();
        if (!message) return;

        sendBtn.disabled = true;
        messageInput.disabled = true;

        fetch(`/admin/chat/${conversationId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addMessage({
                    sender_type: 'admin',
                    sender_label: '{{ auth()->user()->name }}',
                    message: message,
                    timestamp: data.timestamp
                });
                messageInput.value = '';
                lastMessageId = data.message.id;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        })
        .finally(() => {
            sendBtn.disabled = false;
            messageInput.disabled = false;
            messageInput.focus();
        });
    });

    // Poll for new messages every 3 seconds
    function pollMessages() {
        fetch(`/admin/chat/${conversationId}/messages?after_id=${lastMessageId}`, {
            headers: {
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    addMessage(msg);
                    lastMessageId = msg.id;
                });
            }
        })
        .catch(error => console.error('Polling error:', error));
    }

    // Start polling
    setInterval(pollMessages, 3000);

    // Take over button
    const takeOverBtn = document.getElementById('takeOverBtn');
    if (takeOverBtn) {
        takeOverBtn.addEventListener('click', function() {
            if (confirm('Take over this conversation from AI?')) {
                fetch(`/admin/chat/${conversationId}/take-over`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        });
    }

    // Release button
    const releaseBtn = document.getElementById('releaseBtn');
    if (releaseBtn) {
        releaseBtn.addEventListener('click', function() {
            if (confirm('Release this conversation back to AI?')) {
                fetch(`/admin/chat/${conversationId}/release`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        });
    }

    // Close conversation button
    const closeConvBtn = document.getElementById('closeConvBtn');
    if (closeConvBtn) {
        closeConvBtn.addEventListener('click', function() {
            if (confirm('Close this conversation?')) {
                fetch(`/admin/chat/${conversationId}/close`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = '{{ route("admin.chat.index") }}';
                    }
                });
            }
        });
    }
});
</script>
@endsection
