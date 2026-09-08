<?php
/**
 * 메뉴 트리를 재귀적으로 렌더링하는 partial.
 *
 * @var list<array<string, mixed>> $nodes
 */
?>
<?php foreach ($nodes as $node): ?>
    <tr
        draggable="true"
        data-menu-id="<?= (int) $node['id'] ?>"
        data-parent-id="<?= $node['parent_id'] !== null ? (int) $node['parent_id'] : '' ?>"
        data-depth="<?= (int) $node['depth'] ?>"
    >
        <td class="admin-menu-tree__handle" title="드래그하여 순서 변경">⠿</td>
        <td>
            <span class="admin-menu-tree__indent" style="padding-left: <?= (((int) $node['depth']) - 1) * 20 ?>px">
                <?= esc($node['name']) ?>
            </span>
        </td>
        <td class="admin-menu-tree__sort-order"><?= (int) $node['sort_order'] ?></td>
        <td>
            <form method="post" action="<?= site_url('admin/menus/' . $node['id'] . '/toggle-visible') ?>" class="admin-inline-form">
                <?= csrf_field() ?>
                <?php if ((int) $node['is_visible'] === 1): ?>
                    <button type="submit" class="admin-badge admin-badge--success admin-badge--button">노출</button>
                <?php else: ?>
                    <button type="submit" class="admin-badge admin-badge--muted admin-badge--button">숨김</button>
                <?php endif; ?>
            </form>
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
