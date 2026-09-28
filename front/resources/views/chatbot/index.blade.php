<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Chatbot - Upskills Academy') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --bg-primary: #ffffff;
            --bg-secondary: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .chatbot-container {
            background: var(--bg-primary);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            height: 600px;
            display: flex;
            flex-direction: column;
        }

        .chatbot-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }

        .chatbot-header h3 {
            margin: 0;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .chatbot-header .status {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        .language-switch {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .language-switch:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: var(--bg-secondary);
        }

        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-end;
        }

        .message.user {
            justify-content: flex-end;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message-content {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 18px;
            font-size: 0.9rem;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message.user .message-content {
            background: var(--primary-color);
            color: white;
            border-bottom-right-radius: 4px;
        }

        .message.bot .message-content {
            background: white;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            border-bottom-left-radius: 4px;
        }

        .message-time {
            font-size: 0.7rem;
            color: var(--text-secondary);
            margin-top: 5px;
            text-align: center;
        }

        .message.user .message-time {
            text-align: right;
        }

        .message.bot .message-time {
            text-align: left;
        }

        .quick-replies {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid var(--border-color);
        }

        .quick-replies h6 {
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quick-reply-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .quick-reply-btn {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .quick-reply-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .admin-transfer-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .send-to-admin-btn {
            background: var(--success-color);
            border: none;
            color: white;
            padding: 12px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            justify-content: center;
        }

        .send-to-admin-btn:hover {
            background: #059669;
            transform: scale(1.02);
        }

        .chat-input {
            padding: 20px;
            background: white;
            border-top: 1px solid var(--border-color);
        }

        .input-group {
            display: flex;
            gap: 10px;
        }

        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .send-btn {
            background: var(--primary-color);
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .send-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        .send-btn:disabled {
            background: var(--text-secondary);
            cursor: not-allowed;
            transform: none;
        }

        .typing-indicator {
            display: none;
            padding: 12px 16px;
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 18px;
            border-bottom-left-radius: 4px;
            margin-bottom: 15px;
        }

        .typing-dots {
            display: flex;
            gap: 4px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--text-secondary);
            border-radius: 50%;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing {
            0%, 80%, 100% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .welcome-message {
            text-align: center;
            padding: 20px;
            color: var(--text-secondary);
        }

        .welcome-message i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        /* RTL Support */
        [dir="rtl"] .language-switch {
            right: auto;
            left: 15px;
        }

        [dir="rtl"] .message.user .message-content {
            border-bottom-right-radius: 18px;
            border-bottom-left-radius: 4px;
        }

        [dir="rtl"] .message.bot .message-content {
            border-bottom-left-radius: 18px;
            border-bottom-right-radius: 4px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .chatbot-container {
                height: 100vh;
                max-width: 100%;
                border-radius: 0;
            }
            
            body {
                padding: 0;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="chatbot-container">
        <!-- Header -->
        <div class="chatbot-header">
            <button class="language-switch" id="languageSwitch">
                <i class="fas fa-globe"></i> {{ app()->getLocale() == 'ar' ? 'EN' : 'AR' }}
            </button>
            <h3><i class="fas fa-robot"></i> {{ __('Upskills Academy') }}</h3>
            <div class="status" id="status">{{ __('Online') }}</div>
        </div>

        <!-- Messages -->
        <div class="chat-messages" id="chatMessages">
            <div class="welcome-message">
                <i class="fas fa-comments"></i>
                <h5>{{ __('Welcome to Upskills Academy') }}</h5>
                <p>{{ __('Start a conversation with our AI assistant') }}</p>
            </div>
        </div>

        <!-- Quick Replies -->
        <div class="quick-replies" id="quickReplies">
            <h6>{{ __('Quick Questions') }}</h6>
            <div class="quick-reply-buttons">
                <button class="quick-reply-btn" data-question="{{ __('chatbot.tell_about_courses') }}">{{ __('chatbot.courses') }}</button>
                <button class="quick-reply-btn" data-question="{{ __('chatbot.what_books') }}">{{ __('chatbot.books') }}</button>
                <button class="quick-reply-btn" data-question="{{ __('chatbot.how_quiz') }}">{{ __('chatbot.quizzes') }}</button>
                <button class="quick-reply-btn" data-question="{{ __('chatbot.what_prices') }}">{{ __('chatbot.pricing') }}</button>
            </div>
            <div class="admin-transfer-section" id="adminTransferSection" style="display: none; margin-top: 15px;">
                <button class="send-to-admin-btn" id="sendToAdminBtn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9ZM19 21H5V3H13V9H19V21Z" fill="currentColor"/>
                    </svg>
                    {{ __('chatbot.send_to_support') }}
                </button>
            </div>
        </div>

        <!-- Input -->
        <div class="chat-input">
            <div class="input-group">
                <input type="text" class="form-control" id="messageInput" placeholder="{{ __('Type your message...') }}" maxlength="1000">
                <button class="send-btn" id="sendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        class ChatBot {
            constructor() {
                this.conversationId = null;
                this.sessionId = this.generateSessionId();
                this.language = '{{ app()->getLocale() }}';
                this.isTyping = false;
                
                this.initializeElements();
                this.bindEvents();
                this.startConversation();
            }

            initializeElements() {
                this.messagesContainer = document.getElementById('chatMessages');
                this.messageInput = document.getElementById('messageInput');
                this.sendBtn = document.getElementById('sendBtn');
                this.languageSwitch = document.getElementById('languageSwitch');
                this.quickReplies = document.getElementById('quickReplies');
                this.status = document.getElementById('status');
                this.adminTransferSection = document.getElementById('adminTransferSection');
                this.sendToAdminBtn = document.getElementById('sendToAdminBtn');
            }

            bindEvents() {
                this.sendBtn.addEventListener('click', () => this.sendMessage());
                this.messageInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.sendMessage();
                });

                this.languageSwitch.addEventListener('click', () => this.toggleLanguage());

                // Quick reply buttons
                document.querySelectorAll('.quick-reply-btn').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const question = e.target.dataset.question;
                        this.messageInput.value = question;
                        this.sendMessage();
                    });
                });

                // Send to admin button
                if (this.sendToAdminBtn) {
                    this.sendToAdminBtn.addEventListener('click', () => this.sendToAdmin());
                }
            }

            generateSessionId() {
                return 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            }

            async startConversation() {
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
                        this.updateStatus('Online');
                    }
                } catch (error) {
                    console.error('Error starting conversation:', error);
                    this.updateStatus('Offline');
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
                    }
                } catch (error) {
                    console.error('Error sending message:', error);
                    this.hideTypingIndicator();
                    this.addBotMessage('Sorry, I encountered an error. Please try again.');
                }

                this.sendBtn.disabled = false;
            }

            addUserMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message user';
                messageDiv.innerHTML = `
                    <div class="message-content">${this.escapeHtml(message)}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                `;
                this.messagesContainer.appendChild(messageDiv);
                this.scrollToBottom();
            }

            addBotMessage(message) {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message bot';
                messageDiv.innerHTML = `
                    <div class="message-content">${this.escapeHtml(message)}</div>
                    <div class="message-time">${this.getCurrentTime()}</div>
                `;
                this.messagesContainer.appendChild(messageDiv);
                this.scrollToBottom();
                
                // Show admin transfer button after 6 messages
                const messageCount = this.messagesContainer.querySelectorAll('.message').length;
                if (messageCount >= 6 && this.adminTransferSection && this.adminTransferSection.style.display === 'none') {
                    this.adminTransferSection.style.display = 'block';
                }
            }

            showTypingIndicator() {
                this.isTyping = true;
                const typingDiv = document.createElement('div');
                typingDiv.className = 'message bot';
                typingDiv.id = 'typingIndicator';
                typingDiv.innerHTML = `
                    <div class="typing-indicator">
                        <div class="typing-dots">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                `;
                this.messagesContainer.appendChild(typingDiv);
                this.scrollToBottom();
            }

            hideTypingIndicator() {
                this.isTyping = false;
                const typingIndicator = document.getElementById('typingIndicator');
                if (typingIndicator) {
                    typingIndicator.remove();
                }
            }

            toggleLanguage() {
                this.language = this.language === 'en' ? 'ar' : 'en';
                this.languageSwitch.innerHTML = `<i class="fas fa-globe"></i> ${this.language === 'ar' ? 'EN' : 'AR'}`;
                
                // Reload page with new language
                window.location.href = `/${this.language}/chatbot`;
            }

            updateStatus(status) {
                this.status.textContent = status;
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
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9ZM19 21H5V3H13V9H19V21Z" fill="currentColor"/>
                        </svg>
                        Send to Support
                    `;
                });
            }
        }

        // Initialize chatbot when page loads
        document.addEventListener('DOMContentLoaded', () => {
            new ChatBot();
        });
    </script>
</body>
</html>
