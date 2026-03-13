<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Gemini AI Chat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 800px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
            font-size: 2em;
        }
        .chat-box {
            background: #f7f7f7;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            min-height: 200px;
            max-height: 400px;
            overflow-y: auto;
        }
        .message {
            margin-bottom: 15px;
            padding: 12px 16px;
            border-radius: 10px;
            line-height: 1.5;
        }
        .user-message {
            background: #667eea;
            color: white;
            margin-left: 20%;
        }
        .ai-message {
            background: white;
            color: #333;
            margin-right: 20%;
            border: 1px solid #e0e0e0;
        }
        .input-group {
            display: flex;
            gap: 10px;
        }
        textarea {
            flex: 1;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            font-family: inherit;
            resize: vertical;
            min-height: 60px;
        }
        textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        button {
            padding: 15px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover { background: #5568d3; }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .loading {
            display: none;
            text-align: center;
            color: #667eea;
            margin-top: 10px;
        }
        .error {
            color: #e53e3e;
            padding: 10px;
            background: #fff5f5;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 Gemini AI Chat</h1>
        
        <div class="chat-box" id="chatBox">
            <div class="message ai-message">
                👋 Hello! I'm Gemini AI. Ask me anything!
            </div>
        </div>

        <div class="input-group">
            <textarea id="question" placeholder="Type your question here..."></textarea>
            <button onclick="askGemini()" id="sendBtn">Send</button>
        </div>
        
        <div class="loading" id="loading">⏳ Thinking...</div>
        <div class="error" id="error"></div>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        const questionInput = document.getElementById('question');
        const sendBtn = document.getElementById('sendBtn');
        const loading = document.getElementById('loading');
        const errorDiv = document.getElementById('error');

        questionInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                askGemini();
            }
        });

        async function askGemini() {
            const question = questionInput.value.trim();
            
            if (!question) {
                showError('Please enter a question');
                return;
            }

            // Add user message to chat
            addMessage(question, 'user');
            questionInput.value = '';
            
            // Show loading
            sendBtn.disabled = true;
            loading.style.display = 'block';
            errorDiv.style.display = 'none';

            try {
                const response = await fetch('<?php echo e(route("gemini.ask")); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question })
                });

                const data = await response.json();

                if (data.success) {
                    addMessage(data.answer, 'ai');
                } else {
                    showError(data.error || 'An error occurred');
                }
            } catch (error) {
                showError('Network error: ' + error.message);
            } finally {
                sendBtn.disabled = false;
                loading.style.display = 'none';
            }
        }

        function addMessage(text, type) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message ${type}-message`;
            messageDiv.textContent = text;
            chatBox.appendChild(messageDiv);
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function showError(message) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html><?php /**PATH C:\Users\User\finalyear\resources\views/gemini-chat.blade.php ENDPATH**/ ?>