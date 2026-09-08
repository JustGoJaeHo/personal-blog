<?= $this->extend('admin/layouts/default') ?>

<?= $this->section('title') ?>카테고리 수정<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?= $this->include('admin/categories/_form') ?>
<?= $this->endSection() ?>
