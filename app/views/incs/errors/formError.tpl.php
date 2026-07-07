
<?php 
try {
    if (isset($errors[$errorName])) : ?>
        <small class="form__error">
            <?php
            foreach ($errors[$errorName] as $error) {
                echo $error . '<br>';
            }
            ?>
        </small>
    <?php endif; 
} catch(Exception $e) {
}
?>
