<?php
$menuItems = [
    ['label' => '대시보드', 'path' => 'admin'],
    ['label' => '메뉴 관리', 'path' => 'admin/menus'],
];
$currentPath = trim(current_url(true)->getPath(), '/');
?>
<aside class="admin-sidebar">
    <div class="admin-sidebar__brand">
        <a href="<?= site_url('admin') ?>">Blog Admin</a>
    </div>
    <nav class="admin-sidebar__nav">
        <ul>
            <?php foreach ($menuItems as $item):
                $isActive = $item['path'] === 'admin'
                    ? $currentPath === 'admin'
                    : str_starts_with($currentPath, $item['path']);
            ?>
                <li>
                    <a href="<?= site_url($item['path']) ?>"
                       class="admin-sidebar__link<?= $isActive ? ' is-active' : '' ?>">
                        <?= esc($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>
