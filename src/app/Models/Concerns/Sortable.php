<?php

namespace App\Models\Concerns;

interface Sortable
{
    /**
     * 주어진 스코프(예: parent_id) 안에서 $ids 순서대로 sort_order를 재배치한다.
     * 스코프에 실제로 속한 id 집합과 $ids가 정확히 일치할 때만 갱신한다.
     *
     * @param list<int>             $ids
     * @param array<string, mixed>  $scope
     *
     * @return bool|null true: 성공, false: 갱신 실패, null: 요청한 id 목록이 실제 스코프와 불일치
     */
    public function reorderWithinScope(array $ids, array $scope = []): ?bool;
}
