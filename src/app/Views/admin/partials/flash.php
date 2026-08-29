<?php
$session = session();
$message = $session->getFlashdata('message');
$errors  = $session->getFlashdata('errors');
?>
<?php if ($message || $errors): ?>
    <div class="admin-toast admin-toast--<?= $errors ? 'error' : 'success' ?>" data-flash-toast>
        <?php if ($message): ?>
            <p><?= esc($message) ?></p>
        <?php endif; ?>
        <?php if ($errors): ?>
            <ul>
                <?php foreach ((array) $errors as $error): ?>
                    <li><?= esc(is_array($error) ? implode(' ', $error) : $error) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>
