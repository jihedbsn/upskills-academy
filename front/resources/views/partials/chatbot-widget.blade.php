<!-- Chatbot Widget -->
<div id="chatbotWidget" class="chatbot-widget">
    <!-- Chat Button -->
    <div class="chatbot-button" id="chatbotButton">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="currentColor"/>
            <path d="M7 9H17V11H7V9ZM7 12H14V14H7V12Z" fill="currentColor"/>
        </svg>
        <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
    </div>

    <!-- Chat Window -->
    <div class="chatbot-window" id="chatbotWindow">
        <div class="chatbot-header">
            <div class="chatbot-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9ZM19 21H5V3H13V9H19V21Z" fill="currentColor"/>
                </svg>
                <span>{{ __('chatbot.swedish_academy_ai') }}</span>
            </div>
            <div class="chatbot-controls">
                <button class="language-toggle" id="widgetLanguageToggle">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 19.93C7.05 19.44 4 16.08 4 12C4 11.38 4.08 10.79 4.21 10.21L9 15V16C9 17.1 9.9 18 11 18V19.93ZM17.9 17.39C17.64 16.58 16.9 16 16 16H15V13C15 12.45 14.55 12 14 12H8V10H10C10.55 10 11 9.55 11 9V7H13C14.1 7 15 6.1 15 5V4.59C17.93 5.78 20 8.65 20 12C20 14.08 19.2 15.97 17.9 17.39Z" fill="currentColor"/>
                    </svg>
                    {{ app()->getLocale() == 'ar' ? 'EN' : 'AR' }}
                </button>
                <button class="minimize-btn" id="minimizeBtn">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 13H5V11H19V13Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="chatbot-messages" id="widgetMessages">
            <div class="welcome-message">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="#2563eb"/>
                </svg>
                <h6>{{ __('chatbot.welcome_message') }}</h6>
                <p>{{ __('chatbot.how_help_today') }}</p>
            </div>
        </div>

        <div class="chatbot-quick-replies" id="widgetQuickReplies">
            <div class="quick-reply-buttons">
                <button class="quick-reply-btn" data-question="{{ __('chatbot.tell_about_courses') }}">{{ __('chatbot.courses') }}</button>
                <button class="quick-reply-btn" data-question="{{ __('chatbot.what_books') }}">{{ __('chatbot.books') }}</button>
                <button class="quick-reply-btn" data-question="{{ __('chatbot.how_quiz') }}">{{ __('chatbot.quizzes') }}</button>
                <button class="quick-reply-btn" data-question="{{ __('chatbot.what_prices') }}">{{ __('chatbot.pricing') }}</button>
            </div>
            <div class="admin-transfer-section" id="adminTransferSection" style="display: none;">
                <button class="send-to-admin-btn" id="sendToAdminBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9ZM19 21H5V3H13V9H19V21Z" fill="currentColor"/>
                    </svg>
                    {{ __('chatbot.send_to_support') }}
                </button>
            </div>
        </div>

        <div class="chatbot-input">
            <div class="input-group">
                <input type="text" class="form-control" id="widgetMessageInput" placeholder="{{ __('chatbot.type_message') }}" maxlength="1000">
                <button class="send-btn" id="widgetSendBtn">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12L2.01 3L2 10L17 12L2 14L2.01 21Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.chatbot-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Inter', sans-serif;
}

.chatbot-button {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #2563eb, #1e40af);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
    transition: all 0.3s ease;
    position: relative;
}

.chatbot-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(37, 99, 235, 0.4);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.chatbot-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: none;
    flex-direction: column;
    overflow: hidden;
}

.chatbot-window.active {
    display: flex;
}

.chatbot-header {
    background: linear-gradient(135deg, #2563eb, #1e40af);
    color: white;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
}

.chatbot-controls {
    display: flex;
    gap: 8px;
}

.language-toggle, .minimize-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    padding: 5px 8px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s ease;
}

.language-toggle:hover, .minimize-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8fafc;
}

.welcome-message {
    text-align: center;
    padding: 20px;
    color: #64748b;
}

.welcome-message h6 {
    margin: 10px 0 5px 0;
    color: #1e293b;
}

.welcome-message p {
    font-size: 12px;
    margin: 0;
}

.widget-message {
    margin-bottom: 12px;
    display: flex;
    align-items: flex-end;
}

.widget-message.user {
    justify-content: flex-end;
}

.widget-message.bot {
    justify-content: flex-start;
}

.widget-message-content {
    max-width: 75%;
    padding: 10px 12px;
    border-radius: 15px;
    font-size: 13px;
    line-height: 1.3;
    word-wrap: break-word;
}

.widget-message.user .widget-message-content {
    background: #2563eb;
    color: white;
    border-bottom-right-radius: 5px;
}

.widget-message.bot .widget-message-content {
    background: white;
    color: #1e293b;
    border: 1px solid #e2e8f0;
    border-bottom-left-radius: 5px;
}

.widget-message-time {
    font-size: 10px;
    color: #64748b;
    margin-top: 3px;
    text-align: center;
}

.widget-message.user .widget-message-time {
    text-align: right;
}

.widget-message.bot .widget-message-time {
    text-align: left;
}

.chatbot-quick-replies {
    padding: 12px 15px;
    background: white;
    border-top: 1px solid #e2e8f0;
}

.quick-reply-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.quick-reply-btn {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #1e293b;
    padding: 6px 10px;
    border-radius: 15px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.quick-reply-btn:hover {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
}

.admin-transfer-section {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
}

.send-to-admin-btn {
    background: #10b981;
    border: none;
    color: white;
    padding: 8px 12px;
    border-radius: 15px;
    font-size: 11px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 5px;
    width: 100%;
    justify-content: center;
}

.send-to-admin-btn:hover {
    background: #059669;
    transform: scale(1.02);
}

.chatbot-input {
    padding: 15px;
    background: white;
    border-top: 1px solid #e2e8f0;
}

.input-group {
    display: flex;
    gap: 8px;
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 8px 15px;
    font-size: 12px;
    outline: none;
    transition: all 0.3s ease;
    flex: 1;
}

.form-control:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.1);
}

.send-btn {
    background: #2563eb;
    border: none;
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 12px;
}

.send-btn:hover {
    background: #1e40af;
    transform: scale(1.05);
}

.send-btn:disabled {
    background: #64748b;
    cursor: not-allowed;
    transform: none;
}

.widget-typing-indicator {
    padding: 10px 12px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 15px;
    border-bottom-left-radius: 5px;
    margin-bottom: 12px;
}

.widget-typing-dots {
    display: flex;
    gap: 3px;
}

.widget-typing-dot {
    width: 6px;
    height: 6px;
    background: #64748b;
    border-radius: 50%;
    animation: widgetTyping 1.4s infinite ease-in-out;
}

.widget-typing-dot:nth-child(1) { animation-delay: -0.32s; }
.widget-typing-dot:nth-child(2) { animation-delay: -0.16s; }

@keyframes widgetTyping {
    0%, 80%, 100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* RTL Support */
[dir="rtl"] .chatbot-widget {
    right: auto;
    left: 20px;
}

[dir="rtl"] .chatbot-window {
    right: auto;
    left: 0;
}

[dir="rtl"] .widget-message.user .widget-message-content {
    border-bottom-right-radius: 15px;
    border-bottom-left-radius: 5px;
}

[dir="rtl"] .widget-message.bot .widget-message-content {
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 5px;
}

/* Responsive */
@media (max-width: 480px) {
    .chatbot-window {
        width: calc(100vw - 40px);
        height: 60vh;
        bottom: 80px;
        right: 0;
    }

    [dir="rtl"] .chatbot-window {
        left: 0;
    }
}

/* Animation for widget appearance */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.chatbot-window.active {
    animation: slideIn 0.3s ease-out;
}

/* Loading animation */
.loading {
    display: inline-block;
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<script>
class ChatBotWidget {
    constructor() {
        this.conversationId = null;
        this.sessionId = this.generateSessionId();
        this.language = '{{ app()->getLocale() }}';
        this.isTyping = false;
        this.isOpen = false;

        this.initializeElements();
        this.bindEvents();
    }

    initializeElements() {
        this.widget = document.getElementById('chatbotWidget');
        this.button = document.getElementById('chatbotButton');
        this.window = document.getElementById('chatbotWindow');
        this.messagesContainer = document.getElementById('widgetMessages');
        this.messageInput = document.getElementById('widgetMessageInput');
        this.sendBtn = document.getElementById('widgetSendBtn');
        this.languageToggle = document.getElementById('widgetLanguageToggle');
        this.minimizeBtn = document.getElementById('minimizeBtn');
        this.notificationBadge = document.getElementById('notificationBadge');
        this.quickReplies = document.getElementById('widgetQuickReplies');
        this.adminTransferSection = document.getElementById('adminTransferSection');
        this.sendToAdminBtn = document.getElementById('sendToAdminBtn');
    }

    bindEvents() {
        this.button.addEventListener('click', () => this.toggleChat());
        this.minimizeBtn.addEventListener('click', () => this.toggleChat());
        this.languageToggle.addEventListener('click', () => this.toggleLanguage());

        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        // Quick reply buttons
        document.querySelectorAll('.quick-reply-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();

                // Prevent duplicate clicks
                if (btn.disabled || this.isLoading) return;
                btn.disabled = true;
                setTimeout(() => btn.disabled = false, 2000);

                const question = e.target.dataset.question;
                this.messageInput.value = question;
                this.sendMessage();
            }, { once: false });
        });

        // Send to admin button
        this.sendToAdminBtn.addEventListener('click', () => this.sendToAdmin());
    }

    generateSessionId() {
        return 'widget_session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    toggleChat() {
        this.isOpen = !this.isOpen;

        if (this.isOpen) {
            this.window.classList.add('active');
            this.startConversation();
            this.hideNotification();
        } else {
            this.window.classList.remove('active');
        }
    }

    async startConversation() {
        if (this.conversationId) return; // Already started

        try {
            const response = await fetch('/chatbot/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    session_id: this.sessionId,
                    language: this.language
                })
            });

            const data = await response.json();

            if (data.success) {
                this.conversationId = data.conversation_id;
                this.addBotMessage(data.welcome_message);
            }
        } catch (error) {
            console.error('Error starting conversation:', error);
        }
    }

    async sendMessage() {
        const message = this.messageInput.value.trim();
        if (!message || this.isTyping) return;

        this.addUserMessage(message);
        this.messageInput.value = '';
        this.sendBtn.disabled = true;
        this.showTypingIndicator();

        try {
            const response = await fetch('/chatbot/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: this.conversationId,
                    language: this.language
                })
            });

            const data = await response.json();

            if (data.success) {
                this.hideTypingIndicator();
                this.addBotMessage(data.bot_response.message);

                // Show notification if chat is closed
                if (!this.isOpen) {
                    this.showNotification();
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTypingIndicator();
            this.addBotMessage('{{ __("chatbot.sorry_error") }}');
        }

        this.sendBtn.disabled = false;
    }

    addUserMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'widget-message user';
        messageDiv.innerHTML = `
            <div class="widget-message-content">${this.escapeHtml(message)}</div>
            <div class="widget-message-time">${this.getCurrentTime()}</div>
        `;
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();
    }

    addBotMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'widget-message bot';
        messageDiv.innerHTML = `
            <div class="widget-message-content">${this.escapeHtml(message)}</div>
            <div class="widget-message-time">${this.getCurrentTime()}</div>
        `;
        this.messagesContainer.appendChild(messageDiv);
        this.scrollToBottom();

        // Show admin transfer button after 6 messages
        const messageCount = this.messagesContainer.querySelectorAll('.widget-message').length;
        if (messageCount >= 6 && this.adminTransferSection.style.display === 'none') {
            this.adminTransferSection.style.display = 'block';
        }
    }

    showTypingIndicator() {
        this.isTyping = true;
        const typingDiv = document.createElement('div');
        typingDiv.className = 'widget-message bot';
        typingDiv.id = 'widgetTypingIndicator';
        typingDiv.innerHTML = `
            <div class="widget-typing-indicator">
                <div class="widget-typing-dots">
                    <div class="widget-typing-dot"></div>
                    <div class="widget-typing-dot"></div>
                    <div class="widget-typing-dot"></div>
                </div>
            </div>
        `;
        this.messagesContainer.appendChild(typingDiv);
        this.scrollToBottom();
    }

    hideTypingIndicator() {
        this.isTyping = false;
        const typingIndicator = document.getElementById('widgetTypingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    toggleLanguage() {
        this.language = this.language === 'en' ? 'ar' : 'en';
        this.languageToggle.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM11 19.93C7.05 19.44 4 16.08 4 12C4 11.38 4.08 10.79 4.21 10.21L9 15V16C9 17.1 9.9 18 11 18V19.93ZM17.9 17.39C17.64 16.58 16.9 16 16 16H15V13C15 12.45 14.55 12 14 12H8V10H10C10.55 10 11 9.55 11 9V7H13C14.1 7 15 6.1 15 5V4.59C17.93 5.78 20 8.65 20 12C20 14.08 19.2 15.97 17.9 17.39Z" fill="currentColor"/>
        </svg> ${this.language === 'ar' ? 'EN' : 'AR'}`;

        // Reload page with new language
        window.location.href = `/${this.language}`;
    }

    showNotification() {
        const currentCount = parseInt(this.notificationBadge.textContent) || 0;
        this.notificationBadge.textContent = currentCount + 1;
        this.notificationBadge.style.display = 'flex';
    }

    hideNotification() {
        this.notificationBadge.style.display = 'none';
        this.notificationBadge.textContent = '0';
    }

    sendToAdmin() {
        if (!this.conversationId) {
            alert('No active conversation to send.');
            return;
        }

        const message = prompt('Please add a message explaining why you need to contact support:');
        if (!message) return;

        this.sendToAdminBtn.disabled = true;
        this.sendToAdminBtn.innerHTML = '<div class="loading"></div> Sending...';

        fetch('/chatbot/send-to-admin', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                conversation_id: this.conversationId,
                message: message
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.addBotMessage(data.message);
                this.adminTransferSection.style.display = 'none';
                this.conversationId = null; // Reset conversation
            } else {
                alert('Error sending to support. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending to support. Please try again.');
        })
        .finally(() => {
            this.sendToAdminBtn.disabled = false;
            this.sendToAdminBtn.innerHTML = `
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9ZM19 21H5V3H13V9H19V21Z" fill="currentColor"/>
                </svg>
                {{ __('chatbot.send_to_support') }}
            `;
        });
    }

    getCurrentTime() {
        return new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

// Initialize chatbot widget when page loads
document.addEventListener('DOMContentLoaded', () => {
    new ChatBotWidget();
});
</script>
