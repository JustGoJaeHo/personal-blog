<?= $this->extend('admin/layouts/default') ?>

<?= $this->section('title') ?>메뉴 관리<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <a href="<?= site_url('admin/menus/create') ?>" class="admin-btn admin-btn--primary">메뉴 추가</a>
</div>

<?php
/** @var list<array<string, mixed>> $tree */
?>

<table class="admin-table">
    <thead>
        <tr>
            <th>메뉴명</th>
            <th>정렬순서</th>
            <th>노출여부</th>
            <th>관리</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tree)): ?>
            <tr>
                <td colspan="4" class="admin-table__empty">등록된 메뉴가 없습니다.</td>
            </tr>
        <?php else: ?>
            <?= view('admin/menus/_tree_rows', ['nodes' => $tree]) ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
