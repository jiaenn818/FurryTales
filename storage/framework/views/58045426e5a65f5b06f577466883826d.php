<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Q&A</title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <style>
        body { font-family: Arial; padding: 20px; }
        input, select, button { padding: 8px; margin: 5px 0; width: 100%; }
        #answer { margin-top: 20px; padding: 10px; border: 1px solid #ccc; white-space: pre-wrap; }
    </style>
</head>
<body>
<h2>Ask a Pet Question</h2>

<form id="qaForm">
    <input type="text" id="question" placeholder="Type your question..." required>
    <select id="category">
        <option value="general">General</option>
        <option value="nutrition">Nutrition</option>
        <option value="health">Health</option>
        <option value="training">Training</option>
    </select>
    <button type="submit">Ask</button>
</form>

<div id="answer"></div>

<script>
document.getElementById('qaForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const answerDiv = document.getElementById('answer');
    answerDiv.textContent = '🤖 Thinking...';

    const question = document.getElementById('question').value;
    const category = document.getElementById('category').value;

    try {
        const res = await fetch("<?php echo e(route('qna.ask')); ?>", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ question, category })
        });

        const data = await res.json();
        answerDiv.textContent = data.success ? data.answer : 'Error: ' + data.error;
    } catch(err) {
        answerDiv.textContent = 'Error: ' + err.message;
    }
});
</script>

</body>
</html>
<?php /**PATH C:\Users\User\finalyear\resources\views/qna.blade.php ENDPATH**/ ?>