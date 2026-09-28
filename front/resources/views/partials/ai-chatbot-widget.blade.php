<!-- AI Chatbot Widget - Clean Design -->
<div id="ai-chatbot-widget" class="chatbot-widget">
    <!-- Chat Toggle Button -->
    <button id="chatbot-toggle" class="chatbot-toggle" aria-label="Open chat">
        <svg class="chat-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <svg class="close-icon" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="chatbot-window">
        <!-- Header -->
        <div class="chatbot-header">
            <span class="header-title">{{ __('chatbot.title') }}</span>
            <div class="header-actions">
                <button id="chatbot-clear" class="header-btn" title="{{ __('chatbot.clear_history') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                </button>
                <button id="chatbot-fullscreen" class="header-btn" title="Fullscreen">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="15 3 21 3 21 9"></polyline>
                        <polyline points="9 21 3 21 3 15"></polyline>
                        <line x1="21" y1="3" x2="14" y2="10"></line>
                        <line x1="3" y1="21" x2="10" y2="14"></line>
                    </svg>
                </button>
                <button id="chatbot-minimize" class="header-btn" title="{{ __('chatbot.minimize') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="chatbot-messages" class="chatbot-messages">
            <!-- Messages will be inserted here -->
        </div>

        <!-- Quick Replies -->
        <div id="chatbot-quick-replies" class="chatbot-quick-replies"></div>

        <!-- Input Area -->
        <div class="chatbot-input-area">
            <input
                type="text"
                id="chatbot-input"
                placeholder="{{ __('chatbot.placeholder') }}"
                maxlength="1000"
                autocomplete="off"
            >
            <button id="chatbot-send" class="send-btn" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                </svg>
            </button>
        </div>
    </div>
</div>

<style>
/* Clean Simple Chatbot Design */
.chatbot-widget {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 99999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

[dir="rtl"] .chatbot-widget {
    right: auto;
    left: 24px;
}

/* Toggle Button */
.chatbot-toggle {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #093162;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 113, 174, 0.4);
    transition: all 0.2s ease;
}

.chatbot-toggle:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 113, 174, 0.5);
}

.chatbot-toggle .close-icon {
    display: none;
}

.chatbot-toggle.active .chat-icon {
    display: none;
}

.chatbot-toggle.active .close-icon {
    display: block;
}

/* Chat Window */
.chatbot-window {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 420px;
    height: 600px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
    display: none;
    flex-direction: column;
    overflow: hidden;
    transition: all 0.3s ease;
}

.chatbot-window.fullscreen {
    position: fixed;
    bottom: 0;
    right: 0;
    width: 100vw;
    height: 100vh;
    border-radius: 0;
    top: 0;
}

[dir="rtl"] .chatbot-window {
    right: auto;
    left: 0;
}

.chatbot-window.open {
    display: flex;
    animation: slideIn 0.2s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Header */
.chatbot-header {
    background: #093162;
    color: white;
    padding: 16px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.header-title {
    font-size: 16px;
    font-weight: 600;
}

.header-actions {
    display: flex;
    gap: 8px;
}

.header-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: rgba(255,255,255,0.15);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.header-btn:hover {
    background: rgba(255,255,255,0.25);
}

/* Messages Container */
.chatbot-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    background: #f5f5f5;
}

.chatbot-messages::-webkit-scrollbar {
    width: 4px;
}

.chatbot-messages::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 2px;
}

/* Message Bubbles */
.message {
    max-width: 85%;
    animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

.message.user {
    align-self: flex-end;
}

.message.bot {
    align-self: flex-start;
}

.message-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.6;
    word-wrap: break-word;
    white-space: pre-line;
}

.message.user .message-bubble {
    background: #093162;
    color: white;
    border-bottom-right-radius: 4px;
}

.message.bot .message-bubble {
    background: white;
    color: #333;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Admin message styling */
.message.admin {
    align-self: flex-start;
}

.message.admin .message-bubble,
.message-bubble.admin-message {
    background: linear-gradient(135deg, #28a745 0%, #20963d 100%);
    color: white;
    border-bottom-left-radius: 4px;
}

.admin-label {
    font-weight: 600;
    font-size: 11px;
    display: block;
    margin-bottom: 4px;
    opacity: 0.9;
}

/* Format lists and structured content */
.message-bubble ol,
.message-bubble ul {
    margin: 8px 0;
    padding-left: 20px;
}

.message-bubble li {
    margin: 4px 0;
}

.message-bubble p {
    margin: 8px 0;
}

.message-bubble p:first-child {
    margin-top: 0;
}

.message-bubble p:last-child {
    margin-bottom: 0;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 14px;
    background: white;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    width: fit-content;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    background: #093162;
    border-radius: 50%;
    animation: bounce 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(1) { animation-delay: 0s; }
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }

@keyframes bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-6px); }
}

/* Quick Replies */
.chatbot-quick-replies {
    padding: 0 20px 12px;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    background: #f5f5f5;
}

.chatbot-quick-replies:empty {
    display: none;
}

.quick-reply-btn {
    padding: 6px 12px;
    background: white;
    color: #093162;
    border: 1px solid #093162;
    border-radius: 16px;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-reply-btn:hover {
    background: #093162;
    color: white;
}

/* Input Area */
.chatbot-input-area {
    padding: 16px 20px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
    align-items: center;
}

#chatbot-input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 12px 16px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s;
}

#chatbot-input:focus {
    border-color: #093162;
}

#chatbot-input::placeholder {
    color: #999;
}

.send-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #093162;
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}

.send-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.send-btn:not(:disabled):hover {
    background: #005a8c;
}

/* Mobile Responsive */
@media (max-width: 480px) {
    .chatbot-widget {
        bottom: 16px;
        right: 16px;
    }

    .chatbot-window {
        width: calc(100vw - 32px);
        height: calc(100vh - 100px);
        bottom: 65px;
        right: -8px;
    }

    .chatbot-window.fullscreen {
        width: 100vw;
        height: 100vh;
        bottom: 0;
        right: 0;
    }

    [dir="rtl"] .chatbot-window {
        right: auto;
        left: -8px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('chatbot-toggle');
    const window_el = document.getElementById('chatbot-window');
    const messages = document.getElementById('chatbot-messages');
    const quickReplies = document.getElementById('chatbot-quick-replies');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const clearBtn = document.getElementById('chatbot-clear');
    const minimizeBtn = document.getElementById('chatbot-minimize');
    const fullscreenBtn = document.getElementById('chatbot-fullscreen');

    let isOpen = false;
    let isLoading = false;
    let isFullscreen = false;
    let adminHandling = false;
    let lastMessageId = 0;
    let pollInterval = null;
    let usedSuggestions = new Set(); // Track which suggestions have been used
    let allQuickReplies = []; // Store all quick replies

    // Toggle chat window
    function toggleChat() {
        isOpen = !isOpen;
        window_el.classList.toggle('open', isOpen);
        toggle.classList.toggle('active', isOpen);

        if (isOpen) {
            if (messages.children.length === 0) {
                initChat();
            }
            startPolling();
        } else {
            stopPolling();
        }
    }

    // Start polling for admin messages
    function startPolling() {
        if (pollInterval) return;
        pollInterval = setInterval(pollMessages, 3000);
    }

    // Stop polling
    function stopPolling() {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }

    // Poll for new messages
    async function pollMessages() {
        try {
            const response = await fetch(`/ai-chat/poll?last_id=${lastMessageId}`);
            const data = await response.json();

            if (data.success && data.messages && data.messages.length > 0) {
                data.messages.forEach(msg => {
                    addMessage(msg.message, msg.sender_type === 'admin' ? 'admin' : 'bot');
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });
            }

            // Update admin handling status
            if (data.admin_handling !== undefined) {
                updateAdminStatus(data.admin_handling);
            }
        } catch (error) {
            console.error('Poll error:', error);
        }
    }

    // Update admin handling status
    function updateAdminStatus(isAdminHandling) {
        adminHandling = isAdminHandling;
        const header = document.querySelector('.chatbot-header .header-title');
        if (header) {
            if (adminHandling) {
                header.innerHTML = '💬 {{ __("chatbot.title") }} <span style="font-size: 11px; background: #28a745; color: white; padding: 2px 6px; border-radius: 10px; margin-left: 8px;">Live Agent</span>';
            } else {
                header.textContent = '{{ __("chatbot.title") }}';
            }
        }
    }

    // Fullscreen toggle
    function toggleFullscreen() {
        isFullscreen = !isFullscreen;
        window_el.classList.toggle('fullscreen', isFullscreen);
    }

    toggle.addEventListener('click', toggleChat);
    minimizeBtn.addEventListener('click', toggleChat);
    fullscreenBtn.addEventListener('click', toggleFullscreen);

    // Initialize chat
    async function initChat() {
        try {
            const response = await fetch('/ai-chat/init');
            const data = await response.json();

            // Load existing messages
            if (data.existing_messages && data.existing_messages.length > 0) {
                data.existing_messages.forEach(msg => {
                    const sender = msg.sender_type === 'user' ? 'user' : (msg.sender_type === 'admin' ? 'admin' : 'bot');
                    addMessage(msg.message, sender);
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });
            } else if (data.welcome_message) {
                addMessage(data.welcome_message, 'bot');
            }

            if (data.quick_replies && data.quick_replies.length > 0) {
                allQuickReplies = data.quick_replies;
                showQuickReplies(allQuickReplies.filter(reply => !usedSuggestions.has(reply)));
            }

            // Update admin status
            if (data.admin_handling !== undefined) {
                updateAdminStatus(data.admin_handling);
            }
        } catch (error) {
            addMessage('Hello! How can I help you today?', 'bot');
        }
    }

    // Add message to chat
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;

        let bubbleClass = 'message-bubble';
        let labelHtml = '';

        if (sender === 'admin') {
            bubbleClass += ' admin-message';
            labelHtml = '<span class="admin-label">👤 Agent:</span>';
        }

        messageDiv.innerHTML = `<div class="${bubbleClass}">${labelHtml}${escapeHtml(text)}</div>`;
        messages.appendChild(messageDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    // Show typing indicator
    function showTyping() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message bot typing';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <span></span><span></span><span></span>
            </div>
        `;
        messages.appendChild(typingDiv);
        messages.scrollTop = messages.scrollHeight;
    }

    // Remove typing indicator
    function removeTyping() {
        const typing = messages.querySelector('.typing');
        if (typing) typing.remove();
    }

    // Show quick replies
    function showQuickReplies(replies) {
        quickReplies.innerHTML = '';
        replies.forEach(reply => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-btn';
            btn.textContent = reply;
            btn.addEventListener('click', () => {
                // Mark this suggestion as used
                usedSuggestions.add(reply);
                // Remove only this button
                btn.remove();
                input.value = reply;
                sendMessage();
            });
            quickReplies.appendChild(btn);
        });
    }

    // Send message
    async function sendMessage() {
        const text = input.value.trim();
        if (!text || isLoading) return;

        addMessage(text, 'user');
        input.value = '';
        sendBtn.disabled = true;
        isLoading = true;

        // Only show typing if not admin handling
        if (!adminHandling) {
            showTyping();
        }

        try {
            const response = await fetch('/ai-chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ message: text })
            });

            removeTyping();

            if (response.ok) {
                const data = await response.json();

                // Update admin status
                if (data.admin_handling !== undefined) {
                    updateAdminStatus(data.admin_handling);
                }

                // Only add bot message if not admin handling (admin will reply via poll)
                if (data.message && !data.admin_handling) {
                    addMessage(data.message, 'bot');
                    // Update lastMessageId to prevent duplicate on next poll
                    if (data.message_id) {
                        lastMessageId = Math.max(lastMessageId, data.message_id);
                    }
                }
            } else {
                addMessage('Unable to connect. Please check your internet connection and try again.', 'bot');
            }
        } catch (error) {
            removeTyping();
            addMessage('Unable to connect. Please check your internet connection and try again.', 'bot');
        }

        isLoading = false;
    }

    // Event listeners
    sendBtn.addEventListener('click', sendMessage);

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    input.addEventListener('input', () => {
        sendBtn.disabled = !input.value.trim();
    });

    // Clear chat
    clearBtn.addEventListener('click', async () => {
        messages.innerHTML = '';
        quickReplies.innerHTML = '';
        usedSuggestions.clear(); // Reset used suggestions
        lastMessageId = 0; // Reset message tracking

        try {
            await fetch('/ai-chat/clear', { method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
            });
        } catch (e) {}

        initChat();
    });

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
