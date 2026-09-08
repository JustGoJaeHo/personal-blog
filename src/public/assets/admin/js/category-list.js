(function () {
    if (typeof AdminReorder === 'undefined') {
        return;
    }

    // 카테고리는 계층이 없는 평면 목록이라 기본 옵션(전체가 한 그룹)만으로 충분하다.
    AdminReorder.init({
        tbodyId: 'category-list-body',
    });
})();
