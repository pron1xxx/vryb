<?php 
$questionId = $question['question']['id'];
?>
<div class="create-test__question">
    <input type="hidden" name="questions[<?= $questionId ?>][question_id]" value="<?= $questionId ?>">
    <div class="input-group">
        <label class="input-group__label">Вопрос</label>
        <input type="text" class="input-group__input" placeholder="Введите вопрос"
               name="questions[<?= $questionId ?>][text]"
               value="<?= htmlspecialchars($question['question']['question_text']) ?>" required data-mask="text" minlength="5">
    </div>

    <label class="input-group__label">Варианты ответов</label>
    <div class="input-group answers">
        <?php foreach($question['answers'] as $index => $answer): ?>
            <input type="hidden" 
                   name="questions[<?= $questionId ?>][answer_ids][<?= $index ?>]" 
                   value="<?= $answer['id'] ?>" required data-mask="text" minlength="5" maxlength="20">
            <input type="text" class="input-group__input" placeholder="Ответ <?= $index + 1 ?>"
                   name="questions[<?= $questionId ?>][answers][]" 
                   value="<?= htmlspecialchars($answer['answer_text']) ?>" required data-mask="number">
        <?php endforeach; ?>
    </div>

    <div class="input-group">
        <label class="input-group__label">Правильный ответ (1-4)</label>
        <input type="number" class="input-group__input" min="1" max="4"
               name="questions[<?= $questionId ?>][correct_answer]"
               value="<?= htmlspecialchars($question['question']['correct_answer']) ?>">
    </div>
    <button type="button" class="remove-question-btn" style="background: #ff4444; color: white; padding: 5px 10px; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;">Удалить вопрос</button>
</div>