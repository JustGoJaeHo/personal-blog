<?php
$session = session();
$message = $session->getFlashdata('message');
$errors  = $session->getFlashdata('errors');
?>
<?php if ($message): ?>
    <div class="admin-alert admin-alert--success"><?= esc($message) ?></div>
<?php endif; ?>
<?php if ($errors): ?>
    <div class="admin-alert admin-alert--error">
        <ul>
            <?php foreach ((array) $errors as $error): ?>
                <li><?= esc(is_array($error) ? implode(' ', $error) : $error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
