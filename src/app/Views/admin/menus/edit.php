<?= $this->extend('admin/layouts/default') ?>

<?= $this->section('title') ?>메뉴 수정<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('admin/menus/_form') ?>
<?= $this->endSection() ?>
