<?= $this->extend('admin/layouts/default') ?>

<?= $this->section('title') ?>메뉴 관리<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <a href="<?= site_url('admin/menus/create') ?>" class="admin-btn admin-btn--primary">메뉴 추가</a>
</div>

<?php
/** @var list<array<string, mixed>> $tree */
$renderRows = function (array $nodes) use (&$renderRows): void {
    foreach ($nodes as $node):
        ?>
        <tr>
            <td>
                <span class="admin-menu-tree__indent" style="padding-left: <?= (((int) $node['depth']) - 1) * 20 ?>px">
                    <?= esc($node['name']) ?>
                </span>
            </td>
            <td><?= (int) $node['sort_order'] ?></td>
            <td>
                <?php if ((int) $node['is_visible'] === 1): ?>
                    <span class="admin-badge admin-badge--success">노출</span>
                <?php else: ?>
                    <span class="admin-badge admin-badge--muted">숨김</span>
                <?php endif; ?>
            </td>
            <td class="admin-table__actions">
                <a href="<?= site_url('admin/menus/' . $node['id'] . '/edit') ?>" class="admin-btn admin-btn--small">수정</a>
                <form method="post" action="<?= site_url('admin/menus/' . $node['id'] . '/delete') ?>"
                      class="admin-inline-form"
                      onsubmit="return confirm('정말 삭제하시겠습니까? 하위 메뉴도 함께 삭제됩니다.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">삭제</button>
                </form>
            </td>
        </tr>
        <?php
        if (! empty($node['children'])) {
            $renderRows($node['children']);
        }
    endforeach;
};
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
            <?php $renderRows($tree); ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
