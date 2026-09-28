<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Test Debug Chatbot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .debug-panel {
            background: #1e293b;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
        }
        .test-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .test-btn:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐛 Test Debug Chatbot</h1>
        
        <div class="debug-panel" id="debugPanel">
            <div>Debug Panel - Cliquez sur les boutons pour tester</div>
        </div>

        <div>
            <h3>Tests API :</h3>
            <button class="test-btn" onclick="testStartConversation()">Test Start Conversation</button>
            <button class="test-btn" onclick="testSendMessage('courses')">Test "courses"</button>
            <button class="test-btn" onclick="testSendMessage('hello')">Test "hello"</button>
            <button class="test-btn" onclick="testSendMessage('books')">Test "books"</button>
            <button class="test-btn" onclick="testSendMessage('quiz')">Test "quiz"</button>
        </div>

        <div style="margin-top: 20px;">
            <h3>Test manuel :</h3>
            <input type="text" id="testInput" placeholder="Tapez votre message..." style="padding: 10px; width: 300px; margin-right: 10px;">
            <button class="test-btn" onclick="testManualMessage()">Envoyer</button>
        </div>
    </div>

    <script>
    let conversationId = null;

    function log(message) {
        const panel = document.getElementById('debugPanel');
        const time = new Date().toLocaleTimeString();
        panel.innerHTML += `<div>[${time}] ${message}</div>`;
        panel.scrollTop = panel.scrollHeight;
        console.log(message);
    }

    async function testStartConversation() {
        log('🔄 Testing start conversation...');
        try {
            const response = await fetch('/chatbot/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    language: 'en'
                })
            });

            const data = await response.json();
            log(`✅ Start response: ${JSON.stringify(data, null, 2)}`);
            
            if (data.success) {
                conversationId = data.conversation_id;
                log(`📝 Conversation ID: ${conversationId}`);
            }
        } catch (error) {
            log(`❌ Error: ${error.message}`);
        }
    }

    async function testSendMessage(message) {
        if (!conversationId) {
            log('❌ No conversation ID. Please start conversation first.');
            return;
        }

        log(`🔄 Testing send message: "${message}"`);
        try {
            const response = await fetch('/chatbot/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: message,
                    conversation_id: conversationId,
                    language: 'en'
                })
            });

            const data = await response.json();
            log(`✅ Send response: ${JSON.stringify(data, null, 2)}`);
            
            if (data.success) {
                log(`🤖 Bot response: ${data.response}`);
            }
        } catch (error) {
            log(`❌ Error: ${error.message}`);
        }
    }

    async function testManualMessage() {
        const message = document.getElementById('testInput').value.trim();
        if (message) {
            await testSendMessage(message);
            document.getElementById('testInput').value = '';
        }
    }

    // Auto-start conversation on page load
    document.addEventListener('DOMContentLoaded', () => {
        log('🚀 Page loaded, auto-starting conversation...');
        testStartConversation();
    });
    </script>
</body>
</html>
