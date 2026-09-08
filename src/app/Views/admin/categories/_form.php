<?php
/**
 * @var array<string, mixed>|null $category
 * @var string $formAction
 */
$category ??= [];
$value = static fn (string $key, $default = '') => old($key, $category[$key] ?? $default);
?>
<form method="post" action="<?= $formAction ?>" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-form__group">
        <label for="name">카테고리명</label>
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
        <a href="<?= site_url('admin/categories') ?>" class="admin-btn">취소</a>
    </div>
</form>
