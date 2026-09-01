<?php
/**
 * 메뉴 트리를 재귀적으로 렌더링하는 partial.
 *
 * @var list<array<string, mixed>> $nodes
 */
?>
<?php foreach ($nodes as $node): ?>
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
    <?php if (! empty($node['children'])): ?>
        <?= view('admin/menus/_tree_rows', ['nodes' => $node['children']]) ?>
    <?php endif; ?>
<?php endforeach; ?>
