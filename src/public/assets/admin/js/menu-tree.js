(function () {
    var tbody = document.getElementById('menu-tree-body');

    if (!tbody) {
        return;
    }

    var reorderUrl = tbody.dataset.reorderUrl;
    var csrfHeader = tbody.dataset.csrfHeader;
    var csrfToken = tbody.dataset.csrfToken;

    var draggedRow = null;
    var dropTargetRow = null;

    function rows() {
        return Array.prototype.slice.call(tbody.querySelectorAll('tr[data-menu-id]'));
    }

    // row 자신과, 화면상 바로 아래로 이어지는 더 깊은 depth의 자식 행들(하위 트리)을 함께 반환한다.
    function subtree(row) {
        var depth = Number(row.dataset.depth);
        var block = [row];
        var next = row.nextElementSibling;

        while (next && Number(next.dataset.depth) > depth) {
            block.push(next);
            next = next.nextElementSibling;
        }

        return block;
    }

    function clearDropTarget() {
        if (dropTargetRow) {
            dropTargetRow.classList.remove('is-drop-target');
            dropTargetRow = null;
        }
    }

    tbody.addEventListener('dragstart', function (event) {
        var row = event.target.closest('tr[data-menu-id]');
        if (!row) {
            return;
        }
        draggedRow = row;
        row.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', row.dataset.menuId);
    });

    tbody.addEventListener('dragover', function (event) {
        if (!draggedRow) {
            return;
        }

        var targetRow = event.target.closest('tr[data-menu-id]');
        var draggedSubtree = subtree(draggedRow);

        var isValidTarget = targetRow
            && targetRow !== draggedRow
            && targetRow.dataset.parentId === draggedRow.dataset.parentId
            && draggedSubtree.indexOf(targetRow) === -1;

        if (!isValidTarget) {
            return;
        }

        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';

        if (dropTargetRow !== targetRow) {
            clearDropTarget();
            dropTargetRow = targetRow;
            dropTargetRow.classList.add('is-drop-target');
        }
    });

    tbody.addEventListener('drop', function (event) {
        if (!draggedRow || !dropTargetRow) {
            return;
        }
        event.preventDefault();

        var targetRow = dropTargetRow;
        var draggedBlock = subtree(draggedRow);
        var targetBlock = subtree(targetRow);
        var rect = targetRow.getBoundingClientRect();
        var insertAfter = event.clientY > rect.top + rect.height / 2;
        var referenceNode = insertAfter
            ? targetBlock[targetBlock.length - 1].nextElementSibling
            : targetBlock[0];

        // referenceNode가 이동 대상(draggedBlock) 안에 있다면 이미 그 위치이므로 이동을 생략한다.
        if (draggedBlock.indexOf(referenceNode) === -1) {
            draggedBlock.forEach(function (row) {
                tbody.insertBefore(row, referenceNode);
            });
        }

        submitOrder(draggedRow.dataset.parentId);
    });

    tbody.addEventListener('dragend', function () {
        if (draggedRow) {
            draggedRow.classList.remove('is-dragging');
        }
        clearDropTarget();
        draggedRow = null;
    });

    function submitOrder(parentId) {
        var siblingRows = rows().filter(function (row) {
            return row.dataset.parentId === parentId;
        });

        var formData = new FormData();
        formData.append('parent_id', parentId);
        siblingRows.forEach(function (row) {
            formData.append('ids[]', row.dataset.menuId);
        });

        var headers = {};
        headers[csrfHeader] = csrfToken;

        fetch(reorderUrl, {
            method: 'POST',
            headers: headers,
            body: formData,
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.data && result.data.csrfToken) {
                    csrfToken = result.data.csrfToken;
                    tbody.dataset.csrfToken = csrfToken;
                }
                if (!result.ok) {
                    throw new Error((result.data && result.data.message) || '순서 변경에 실패했습니다.');
                }
                siblingRows.forEach(function (row, index) {
                    var cell = row.querySelector('.admin-menu-tree__sort-order');
                    if (cell) {
                        cell.textContent = String(index + 1);
                    }
                });
            })
            .catch(function (error) {
                alert(error.message || '순서 변경에 실패했습니다.');
                window.location.reload();
            });
    }
})();
