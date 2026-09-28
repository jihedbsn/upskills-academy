<!-- Chatbot Widget Simple -->
<div id="chatbotWidget" class="chatbot-widget" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
    <!-- Chat Button -->
    <div class="chatbot-button" id="chatbotButton" style="width: 60px; height: 60px; background: linear-gradient(135deg, #2563eb, #1e40af); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px; cursor: pointer; box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3); transition: all 0.3s ease; position: relative;">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="currentColor"/>
            <path d="M7 9H17V11H7V9ZM7 12H14V14H7V12Z" fill="currentColor"/>
        </svg>
    </div>
    
    <!-- Chat Window -->
    <div class="chatbot-window" id="chatbotWindow" style="position: absolute; bottom: 80px; right: 0; width: 350px; height: 500px; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15); display: none; flex-direction: column; overflow: hidden;">
        <div class="chatbot-header" style="background: linear-gradient(135deg, #2563eb, #1e40af); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <div class="chatbot-title" style="display: flex; align-items: center; gap: 8px; font-weight: 600; font-size: 14px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 1H5C3.89 1 3 1.89 3 3V21C3 22.11 3.89 23 5 23H19C20.11 23 21 22.11 21 21V9ZM19 21H5V3H13V9H19V21Z" fill="currentColor"/>
                </svg>
                <span>Upskills Academy</span>
            </div>
            <button class="minimize-btn" id="minimizeBtn" style="background: rgba(255, 255, 255, 0.2); border: none; color: white; padding: 5px 8px; border-radius: 8px; cursor: pointer; font-size: 12px; transition: all 0.3s ease;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 13H5V11H19V13Z" fill="currentColor"/>
                </svg>
            </button>
        </div>
        
        <div class="chatbot-messages" id="widgetMessages" style="flex: 1; padding: 15px; overflow-y: auto; background: #f8fafc;">
            <div class="welcome-message" style="text-align: center; padding: 20px; color: #64748b;">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill="#2563eb"/>
                </svg>
                <h6 style="margin: 10px 0 5px 0; color: #1e293b;">Welcome!</h6>
                <p style="font-size: 12px; margin: 0;">How can I help you today?</p>
            </div>
            
            <!-- Quick Reply Buttons -->
            <div class="quick-replies" style="margin-top: 15px;">
                <div style="display: flex; flex-wrap: wrap; gap: 8px; justify-content: center;">
                    <button class="quick-reply-btn" data-message="courses" style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 16px; font-size: 12px; cursor: pointer; transition: all 0.3s ease; color: #64748b;">Courses</button>
                    <button class="quick-reply-btn" data-message="books" style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 16px; font-size: 12px; cursor: pointer; transition: all 0.3s ease; color: #64748b;">Books</button>
                    <button class="quick-reply-btn" data-message="quiz" style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 16px; font-size: 12px; cursor: pointer; transition: all 0.3s ease; color: #64748b;">Quiz</button>
                    <button class="quick-reply-btn" data-message="hello" style="background: white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 16px; font-size: 12px; cursor: pointer; transition: all 0.3s ease; color: #64748b;">Hello</button>
                </div>
            </div>
        </div>
        
        <div class="chatbot-input" style="padding: 15px; background: white; border-top: 1px solid #e2e8f0;">
            <div class="input-group" style="display: flex; gap: 8px;">
                <input type="text" class="form-control" id="widgetMessageInput" placeholder="Type your message..." maxlength="1000" style="border: 1px solid #e2e8f0; border-radius: 20px; padding: 8px 15px; font-size: 12px; outline: none; transition: all 0.3s ease; flex: 1;">
                <button class="send-btn" id="widgetSendBtn" style="background: #2563eb; border: none; color: white; width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; font-size: 12px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.01 21L23 12L2.01 3L2 10L17 12L2 14L2.01 21Z" fill="currentColor"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
class ChatBotWidgetSimple {
    constructor() {
        this.isOpen = false;
        this.conversationId = null;
        this.language = 'en';
        this.initializeElements();
        this.bindEvents();
        this.initializeConversation();
    }

    initializeElements() {
        this.button = document.getElementById('chatbotButton');
        this.window = document.getElementById('chatbotWindow');
        this.minimizeBtn = document.getElementById('minimizeBtn');
        this.messageInput = document.getElementById('widgetMessageInput');
        this.sendBtn = document.getElementById('widgetSendBtn');
        this.messagesContainer = document.getElementById('widgetMessages');
    }

    bindEvents() {
        this.button.addEventListener('click', () => this.toggleChat());
        this.minimizeBtn.addEventListener('click', () => this.toggleChat());
        
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.messageInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') this.sendMessage();
        });

        // Quick reply buttons
        document.querySelectorAll('.quick-reply-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const message = e.target.dataset.message;
                this.messageInput.value = message;
                this.sendMessage();
            });
        });
    }

    async initializeConversation() {
        try {
            const response = await fetch('/chatbot/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    language: this.language
                })
            });

            const data = await response.json();
            if (data.success) {
                this.conversationId = data.conversation_id;
                console.log('Conversation initialized:', this.conversationId);
            }
        } catch (error) {
            console.error('Error initializing conversation:', error);
        }
    }

    toggleChat() {
        this.isOpen = !this.isOpen;
        
        if (this.isOpen) {
            this.window.style.display = 'flex';
            this.messageInput.focus();
        } else {
            this.window.style.display = 'none';
        }
    }

    async sendMessage() {
        const message = this.messageInput.value.trim();
        if (!message) return;

        // Add user message
        this.addMessage(message, 'user');
        this.messageInput.value = '';

        // Show typing indicator
        this.showTypingIndicator();

        try {
            const response = await fetch('/chatbot/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: this.conversationId,
                    language: this.language
                })
            });

            const data = await response.json();
            
            // Hide typing indicator
            this.hideTypingIndicator();

            if (data.success) {
                this.addMessage(data.response, 'bot');
                
                // Check if this is a default response (bot didn't understand)
                const defaultResponses = [
                    "Thank you for your question. I'm sorry, but I didn't understand your question clearly",
                    "شكراً لك على سؤالك. للأسف، لم أفهم سؤالك بشكل واضح"
                ];
                
                const isDefaultResponse = defaultResponses.some(defaultText => 
                    data.response.includes(defaultText)
                );
                
                if (isDefaultResponse) {
                    this.addSupportButton(message);
                }
            } else {
                this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTypingIndicator();
            this.addMessage('Sorry, I encountered an error. Please try again.', 'bot');
        }
    }

    addSupportButton(originalMessage) {
        const messageDiv = document.createElement('div');
        messageDiv.style.marginTop = '10px';
        messageDiv.style.textAlign = 'center';

        const supportBtn = document.createElement('button');
        supportBtn.textContent = this.language === 'ar' ? 'إرسال إلى الدعم' : 'Send to Support';
        supportBtn.style.background = '#10b981';
        supportBtn.style.color = 'white';
        supportBtn.style.border = 'none';
        supportBtn.style.padding = '8px 16px';
        supportBtn.style.borderRadius = '20px';
        supportBtn.style.fontSize = '12px';
        supportBtn.style.cursor = 'pointer';
        supportBtn.style.margin = '5px';

        supportBtn.addEventListener('click', () => {
            this.sendToSupport(originalMessage);
        });

        messageDiv.appendChild(supportBtn);
        this.messagesContainer.appendChild(messageDiv);
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    async sendToSupport(originalMessage) {
        const userMessage = prompt(this.language === 'ar' ? 
            'أضف أي تفاصيل إضافية تريد إرسالها إلى فريق الدعم:' : 
            'Add any additional details you want to send to our support team:'
        );

        if (userMessage === null) return; // User cancelled

        const fullMessage = `Original question: ${originalMessage}\n\nAdditional details: ${userMessage}`;

        try {
            const response = await fetch('/chatbot/send-to-admin', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    conversation_id: this.conversationId,
                    message: fullMessage
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.addMessage(this.language === 'ar' ? 
                    'تم إرسال سؤالك إلى فريق الدعم. سنتواصل معك قريباً!' : 
                    'Your question has been sent to our support team. We will get back to you soon!', 'bot');
            } else {
                this.addMessage(this.language === 'ar' ? 
                    'عذراً، حدث خطأ في إرسال سؤالك. يرجى المحاولة مرة أخرى.' : 
                    'Sorry, there was an error sending your question. Please try again.', 'bot');
            }
        } catch (error) {
            console.error('Error sending to support:', error);
            this.addMessage(this.language === 'ar' ? 
                'عذراً، حدث خطأ في إرسال سؤالك. يرجى المحاولة مرة أخرى.' : 
                'Sorry, there was an error sending your question. Please try again.', 'bot');
        }
    }

    showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.style.marginBottom = '12px';
        typingDiv.style.display = 'flex';
        typingDiv.style.alignItems = 'flex-end';
        typingDiv.style.justifyContent = 'flex-start';

        const typingContent = document.createElement('div');
        typingContent.style.maxWidth = '75%';
        typingContent.style.padding = '10px 12px';
        typingContent.style.borderRadius = '15px';
        typingContent.style.fontSize = '13px';
        typingContent.style.background = 'white';
        typingContent.style.border = '1px solid #e2e8f0';
        typingContent.style.borderBottomLeftRadius = '5px';
        typingContent.innerHTML = '<span style="color: #64748b;">Typing...</span>';

        typingDiv.appendChild(typingContent);
        this.messagesContainer.appendChild(typingDiv);
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    hideTypingIndicator() {
        const typingIndicator = document.getElementById('typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }

    addMessage(message, sender) {
        // Remove typing indicator if present
        this.hideTypingIndicator();

        const messageDiv = document.createElement('div');
        messageDiv.style.marginBottom = '12px';
        messageDiv.style.display = 'flex';
        messageDiv.style.alignItems = 'flex-end';
        messageDiv.style.justifyContent = sender === 'user' ? 'flex-end' : 'flex-start';

        const messageContent = document.createElement('div');
        messageContent.style.maxWidth = '75%';
        messageContent.style.padding = '10px 12px';
        messageContent.style.borderRadius = '15px';
        messageContent.style.fontSize = '13px';
        messageContent.style.lineHeight = '1.3';
        messageContent.style.wordWrap = 'break-word';
        messageContent.style.background = sender === 'user' ? '#2563eb' : 'white';
        messageContent.style.color = sender === 'user' ? 'white' : '#1e293b';
        messageContent.style.border = sender === 'bot' ? '1px solid #e2e8f0' : 'none';
        messageContent.style.borderBottomRightRadius = sender === 'user' ? '5px' : '15px';
        messageContent.style.borderBottomLeftRadius = sender === 'bot' ? '5px' : '15px';
        messageContent.textContent = message;

        messageDiv.appendChild(messageContent);
        this.messagesContainer.appendChild(messageDiv);
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
}

// Initialize chatbot widget when page loads
document.addEventListener('DOMContentLoaded', () => {
    new ChatBotWidgetSimple();
});
</script>
