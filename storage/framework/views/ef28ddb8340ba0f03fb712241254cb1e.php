

<?php $__env->startSection('content'); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>

<div class="qna-container">
    <!-- Top: FAQ Q&A (full width) -->
    <div class="faq-section">
        <h2 class="section-title">❓ Quick Q&A</h2>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">🐶 How often should I walk my dog?</div>
            <div class="faq-answer">Dogs usually need at least 30 minutes to 2 hours of exercise daily, depending on their breed and age.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">🐱 Can I feed my cat human food?</div>
            <div class="faq-answer">Most human foods are not safe for cats. Avoid chocolate, onions, garlic, and dairy products.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">🍖 How often should I feed my dog or cat?</div>
            <div class="faq-answer">Puppies/kittens usually need 3-4 meals per day, adult pets 1-2 meals. Adjust based on breed, age, and weight.</div>
        </div>
        <div class="faq-item">
            <div class="faq-question" onclick="toggleAnswer(this)">🚚 How long does delivery take?</div>
            <div class="faq-answer">Usually within 1-3 business days depending on location.</div>
        </div>
    </div>

    <!-- Bottom Section: side by side -->
    <div class="qna-bottom-section">
        <!-- Left: Popular Questions -->
        <div class="preset-questions-section">
            <h2 class="section-title">💡 Popular Questions</h2>
            <div class="preset-questions-grid">
                <div class="preset-question-card" onclick="askPresetQuestion('What vaccinations does my puppy need?')">
                    <span class="preset-question-icon">💉</span>
                    <span class="preset-question-text">What vaccinations does my puppy need?</span>
                </div>
                <div class="preset-question-card" onclick="askPresetQuestion('What is the best diet for my puppy?')">
                    <span class="preset-question-icon">🍽️</span>
                    <span class="preset-question-text">What is the best diet for my puppy?</span>
                </div>
                <div class="preset-question-card" onclick="askPresetQuestion('How do I calm my anxious pet?')">
                    <span class="preset-question-icon">😰</span>
                    <span class="preset-question-text">How do I calm my anxious pet?</span>
                </div>
                <div class="preset-question-card" onclick="askPresetQuestion('How to choose the right pet for me?')">
                    <span class="preset-question-icon">🐶</span>
                    <span class="preset-question-text">How to choose the right pet for me?</span>
                </div>
                <div class="preset-question-card" onclick="askPresetQuestion('Can my pet take human medicine if they’re sick?')">
                    <span class="preset-question-icon">💊</span>
                    <span class="preset-question-text">Can my pet take human medicine if they’re sick?</span>
                </div>
            </div>
        </div>

        <!-- Right: Gemini AI Chat -->
        <div class="chat-section">
            <h2 class="section-title">💬 Chat with Gemini AI</h2>
            <div class="chat-box" id="chatBox">
                <div class="message ai-message">
                    <div class="message-label">🤖 Gemini AI</div>
                    👋 Hello! I'm here to help answer your pet care questions.
                </div>
            </div>

            <div class="input-group">
                <textarea id="question" class="question-input" placeholder="Type your pet care question here..." rows="2"></textarea>
                <button onclick="askGemini()" id="sendBtn" class="send-button">Send</button>
            </div>

            <div class="loading" id="loading">⏳ Thinking...</div>
            <div class="error" id="error"></div>
        </div>
    </div>
</div>

<script>
    // Toggle FAQ answer visibility
    function toggleAnswer(element) {
        const answer = element.nextElementSibling;
        if (answer.style.display === "block") {
            answer.style.display = "none";
        } else {
            answer.style.display = "block";
        }
    }

    // --- Existing chat JS ---
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

    function askPresetQuestion(question) {
        questionInput.value = question;
        askGemini();
    }

    async function askGemini() {
        const question = questionInput.value.trim();
        if (!question) { showError('Please enter a question'); return; }

        addMessage(question, 'user');
        questionInput.value = '';
        
        sendBtn.disabled = true;
        loading.style.display = 'block';
        errorDiv.style.display = 'none';

        try {
           const response = await fetch('<?php echo e(route("client.help.ask")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ question })
        });

        let data;
        try { data = await response.json(); } 
        catch (e) { showError('⚠️ Invalid response from server. Please try again.'); return; }

        if (!data.success) {
            if (response.status === 429) showError('⚠️ Daily quota exceeded. Please try again tomorrow!');
            else if (response.status === 503) showError('⏳ The AI is busy right now. Please try again in a moment.');
            else showError(data.error || 'An error occurred');
            return;
        }

        addMessage(data.answer, 'ai');

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
        
        const label = document.createElement('div');
        label.className = 'message-label';
        label.textContent = type === 'user' ? '👤 You' : '🤖 Gemini AI';
        
        const content = document.createElement('div');
        content.textContent = text;
        
        messageDiv.appendChild(label);
        messageDiv.appendChild(content);
        chatBox.appendChild(messageDiv);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function showError(message) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        setTimeout(() => { errorDiv.style.display = 'none'; }, 5000);
    }
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\User\finalyear\resources\views/Client/help.blade.php ENDPATH**/ ?>