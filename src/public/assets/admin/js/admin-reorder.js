/**
 * 관리자 목록 화면 공통 드래그 앤 드롭 정렬.
 *
 * tbody에 data-reorder-url / data-csrf-header / data-csrf-token, 각 행에 data-row-id를
 * 요구한다. 계층이 있는 목록(getGroupKey/getBlock)과 없는 평면 목록 둘 다 지원한다.
 */
(function (global) {
    function init(options) {
        var tbody = document.getElementById(options.tbodyId);
        if (!tbody) {
            return;
        }

        var reorderUrl = tbody.dataset.reorderUrl;
        var csrfHeader = tbody.dataset.csrfHeader;
        var csrfToken = tbody.dataset.csrfToken;

        var getGroupKey = options.getGroupKey || function () { return ''; };
        var getBlock = options.getBlock || function (row) { return [row]; };
        var buildFormData = options.buildFormData || function () {};

        var draggedRow = null;
        var dropTargetRow = null;

        function rows() {
            return Array.prototype.slice.call(tbody.querySelectorAll('tr[data-row-id]'));
        }

        function clearDropTarget() {
            if (dropTargetRow) {
                dropTargetRow.classList.remove('is-drop-target');
                dropTargetRow = null;
            }
        }

        tbody.addEventListener('dragstart', function (event) {
            var row = event.target.closest('tr[data-row-id]');
            if (!row) {
                return;
            }
            draggedRow = row;
            row.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', row.dataset.rowId);
        });

        tbody.addEventListener('dragover', function (event) {
            if (!draggedRow) {
                return;
            }

            var targetRow = event.target.closest('tr[data-row-id]');
            var draggedBlock = getBlock(draggedRow);

            var isValidTarget = targetRow
                && targetRow !== draggedRow
                && getGroupKey(targetRow) === getGroupKey(draggedRow)
                && draggedBlock.indexOf(targetRow) === -1;

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
            var draggedBlock = getBlock(draggedRow);
            var targetBlock = getBlock(targetRow);
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

            submitOrder(getGroupKey(draggedRow));
        });

        tbody.addEventListener('dragend', function () {
            if (draggedRow) {
                draggedRow.classList.remove('is-dragging');
            }
            clearDropTarget();
            draggedRow = null;
        });

        function submitOrder(groupKey) {
            var siblingRows = rows().filter(function (row) {
                return getGroupKey(row) === groupKey;
            });

            var formData = new FormData();
            buildFormData(formData, groupKey);
            siblingRows.forEach(function (row) {
                formData.append('ids[]', row.dataset.rowId);
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
                        var cell = row.querySelector('[data-sort-order]');
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
    }

    global.AdminReorder = { init: init };
})(window);
