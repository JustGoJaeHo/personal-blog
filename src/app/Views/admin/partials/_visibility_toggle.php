<?php
/**
 * 노출/숨김 뱃지를 토글 버튼으로 렌더링하는 partial. 메뉴/카테고리 등에서 공통으로 쓴다.
 *
 * @var string $toggleUrl
 * @var bool   $isVisible
 */
?>
<form method="post" action="<?= $toggleUrl ?>" class="admin-inline-form">
    <?= csrf_field() ?>
    <?php if ($isVisible): ?>
        <button type="submit" class="admin-badge admin-badge--success admin-badge--button">노출</button>
    <?php else: ?>
        <button type="submit" class="admin-badge admin-badge--muted admin-badge--button">숨김</button>
    <?php endif; ?>
</form>
