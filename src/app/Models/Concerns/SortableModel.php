<?php

namespace App\Models\Concerns;

/**
 * Sortable 인터페이스의 공통 구현. sort_order 컬럼을 가진 Model에서 사용한다.
 *
 * CodeIgniter\Model을 extends한 클래스에서만 사용하는 것을 전제로 한다.
 * 트레이트는 어떤 클래스와도 상속 관계가 없어 $this의 타입을 IDE가 알 수 없으므로,
 * 여기서 실제로 호출하는 Model의 메서드를 전부 @method로 명시해둔다.
 * (findAll()/update()는 Model에 실제로 선언돼 있고, where()/transStart()/transComplete()/
 * transStatus()는 Model::__call()이 DB 커넥션/쿼리 빌더로 위임하는 매직 메서드다.)
 *
 * @method static                    where(array|callable|string $key, array|bool|float|int|object|string|null $value = null, ?bool $escape = null)
 * @method list<array<string,mixed>> findAll(?int $limit = null, int $offset = 0)
 * @method bool                      update(int|list<int|string>|string|null $id = null, array|object|null $row = null)
 * @method bool                      transStart(bool $testMode = false)
 * @method bool                      transComplete()
 * @method bool                      transStatus()
 */
trait SortableModel
{
    public function reorderWithinScope(array $ids, array $scope = []): ?bool
    {
        if ($ids === []) {
            return null;
        }

        $query = $this;
        foreach ($scope as $field => $value) {
            $query = $query->where($field, $value);
        }

        $actualIds = array_map(
            static fn (array $row): int => (int) $row['id'],
            $query->findAll(),
        );

        $sortedActual    = $actualIds;
        $sortedRequested = $ids;
        sort($sortedActual);
        sort($sortedRequested);

        if ($sortedActual === [] || $sortedActual !== $sortedRequested) {
            return null;
        }

        $this->transStart();
        foreach ($ids as $index => $id) {
            $this->update($id, ['sort_order' => $index + 1]);
        }
        $this->transComplete();

        return $this->transStatus();
    }
}
