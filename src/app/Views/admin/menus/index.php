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
            <th></th>
            <th>메뉴명</th>
            <th>정렬순서</th>
            <th>노출여부</th>
            <th>관리</th>
        </tr>
    </thead>
    <tbody
        id="menu-tree-body"
        data-reorder-url="<?= site_url('admin/menus/reorder') ?>"
        data-csrf-header="<?= esc(csrf_header(), 'attr') ?>"
        data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>"
    >
        <?php if (empty($tree)): ?>
            <tr>
                <td colspan="5" class="admin-table__empty">등록된 메뉴가 없습니다.</td>
            </tr>
        <?php else: ?>
            <?= view('admin/menus/_tree_rows', ['nodes' => $tree]) ?>
        <?php endif; ?>
    </tbody>
</table>

<p class="admin-hint">행 왼쪽의 <span aria-hidden="true">⠿</span> 를 드래그하면 같은 상위 메뉴 안에서 순서를 바꿀 수 있습니다.</p>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/admin/js/menu-tree.js') ?>?v=<?= filemtime(FCPATH . 'assets/admin/js/menu-tree.js') ?>"></script>
<?= $this->endSection() ?>
