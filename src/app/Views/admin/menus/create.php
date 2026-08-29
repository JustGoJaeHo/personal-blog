<?= $this->extend('admin/layouts/default') ?>

<?= $this->section('title') ?>메뉴 등록<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('admin/menus/_form') ?>
<?= $this->endSection() ?>
