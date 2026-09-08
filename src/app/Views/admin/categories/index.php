<?= $this->extend('admin/layouts/default') ?>

<?= $this->section('title') ?>카테고리 관리<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <a href="<?= site_url('admin/categories/create') ?>" class="admin-btn admin-btn--primary">카테고리 추가</a>
</div>

<?php
/** @var list<array<string, mixed>> $categories */
?>

<table class="admin-table">
    <thead>
        <tr>
            <th></th>
            <th>카테고리명</th>
            <th>정렬순서</th>
            <th>노출여부</th>
            <th>관리</th>
        </tr>
    </thead>
    <tbody
        id="category-list-body"
        data-reorder-url="<?= site_url('admin/categories/reorder') ?>"
        data-csrf-header="<?= esc(csrf_header(), 'attr') ?>"
        data-csrf-token="<?= esc(csrf_hash(), 'attr') ?>"
    >
        <?php if (empty($categories)): ?>
            <tr>
                <td colspan="5" class="admin-table__empty">등록된 카테고리가 없습니다.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categories as $category): ?>
                <tr draggable="true" data-row-id="<?= (int) $category['id'] ?>">
                    <td class="admin-drag-handle" title="드래그하여 순서 변경">⠿</td>
                    <td><?= esc($category['name']) ?></td>
                    <td data-sort-order><?= (int) $category['sort_order'] ?></td>
                    <td>
                        <?= view('admin/partials/_visibility_toggle', [
                            'toggleUrl' => site_url('admin/categories/' . $category['id'] . '/toggle-visible'),
                            'isVisible' => (int) $category['is_visible'] === 1,
                        ]) ?>
                    </td>
                    <td class="admin-table__actions">
                        <a href="<?= site_url('admin/categories/' . $category['id'] . '/edit') ?>" class="admin-btn admin-btn--small">수정</a>
                        <form method="post" action="<?= site_url('admin/categories/' . $category['id'] . '/delete') ?>"
                              class="admin-inline-form"
                              onsubmit="return confirm('정말 삭제하시겠습니까?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="admin-btn admin-btn--small admin-btn--danger">삭제</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<p class="admin-hint">행 왼쪽의 <span aria-hidden="true">⠿</span> 를 드래그하면 순서를 바꿀 수 있습니다.</p>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/admin/js/category-list.js') ?>?v=<?= filemtime(FCPATH . 'assets/admin/js/category-list.js') ?>"></script>
<?= $this->endSection() ?>
