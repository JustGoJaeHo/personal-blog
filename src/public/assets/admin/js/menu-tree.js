(function () {
    if (typeof AdminReorder === 'undefined') {
        return;
    }

    AdminReorder.init({
        tbodyId: 'menu-tree-body',

        // 같은 상위 메뉴(parent_id)를 가진 행끼리만 순서를 바꿀 수 있다.
        getGroupKey: function (row) {
            return row.dataset.parentId;
        },

        // 메뉴 트리는 부모 바로 뒤에 그 자식들이 이어지는 구조라, depth가 더 깊은
        // 다음 행들을 하위 트리로 함께 옮긴다.
        getBlock: function (row) {
            var depth = Number(row.dataset.depth);
            var block = [row];
            var next = row.nextElementSibling;

            while (next && Number(next.dataset.depth) > depth) {
                block.push(next);
                next = next.nextElementSibling;
            }

            return block;
        },

        buildFormData: function (formData, groupKey) {
            formData.append('parent_id', groupKey);
        },
    });
})();
