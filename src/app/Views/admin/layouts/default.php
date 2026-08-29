<?php
$pageTitle = $this->renderSection('title', true) ?: '대시보드';
$this->setVar('pageTitle', $pageTitle);
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title><?= esc($pageTitle) ?> · Blog Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/admin/css/layout.css') ?>?v=<?= filemtime(FCPATH . 'assets/admin/css/layout.css') ?>">
</head>
<body>
    <div class="admin-layout">
        <?= $this->include('admin/partials/sidebar') ?>
        <div class="admin-main">
            <?= $this->include('admin/partials/header') ?>
            <main class="admin-content">
                <?= $this->include('admin/partials/flash') ?>
                <?= $this->renderSection('content') ?>
            </main>
        </div>
    </div>
    <script src="<?= base_url('assets/admin/js/flash.js') ?>?v=<?= filemtime(FCPATH . 'assets/admin/js/flash.js') ?>"></script>
</body>
</html>
