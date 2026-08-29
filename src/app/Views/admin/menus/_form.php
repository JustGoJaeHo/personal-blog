<?php
/**
 * @var array<string, mixed>|null $menu
 * @var list<array<string, mixed>> $parents
 * @var string $formAction
 */
$menu ??= [];
$value = static fn (string $key, $default = '') => old($key, $menu[$key] ?? $default);
?>
<form method="post" action="<?= $formAction ?>" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-form__group">
        <label for="parent_id">상위 메뉴</label>
        <select name="parent_id" id="parent_id">
            <option value="">— 없음 (대분류) —</option>
            <?php foreach ($parents as $parent): ?>
                <option value="<?= $parent['id'] ?>" <?= (string) $value('parent_id') === (string) $parent['id'] ? 'selected' : '' ?>>
                    <?= str_repeat('　', $parent['depth'] - 1) . esc($parent['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="admin-form__group">
        <label for="name">메뉴명</label>
        <input type="text" name="name" id="name" value="<?= esc($value('name')) ?>" maxlength="100" required>
    </div>

    <div class="admin-form__group">
        <label for="sort_order">정렬 순서</label>
        <input type="number" name="sort_order" id="sort_order" value="<?= esc((string) $value('sort_order', 0)) ?>" min="0">
    </div>

    <div class="admin-form__group admin-form__group--checkbox">
        <label>
            <input type="checkbox" name="is_visible" value="1" <?= (int) $value('is_visible', 1) === 1 ? 'checked' : '' ?>>
            노출
        </label>
    </div>

    <div class="admin-form__actions">
        <button type="submit" class="admin-btn admin-btn--primary">저장</button>
        <a href="<?= site_url('admin/menus') ?>" class="admin-btn">취소</a>
    </div>
</form>
