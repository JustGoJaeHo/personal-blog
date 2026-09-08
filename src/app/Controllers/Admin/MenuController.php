<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Menu;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class MenuController extends BaseController
{
    private const MAX_DEPTH = 3;

    private Menu $menuModel;

    public function __construct()
    {
        $this->menuModel = new Menu();
    }

    public function index(): string
    {
        $menus = $this->menuModel
            ->orderBy('parent_id', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return view('admin/menus/index', [
            'tree' => $this->buildTree($menus),
        ]);
    }

    public function create(): string
    {
        return view('admin/menus/create', [
            'menu'        => null,
            'parents'     => $this->getAvailableParents(),
            'formAction'  => site_url('admin/menus'),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->validate($this->validationRules())) {
            return $this->validationErrorRedirect($this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id') ?: null;

        $depth = $this->resolveDepth($parentId);
        if ($depth === null) {
            return $this->validationErrorRedirect(['parent_id' => '유효하지 않은 상위 메뉴입니다.']);
        }

        $this->menuModel->insert($this->buildMenuPayload($parentId, $depth));

        return redirect()->to('/admin/menus')->with('message', '메뉴가 등록되었습니다.');
    }

    public function edit(int $id): string
    {
        $menu = $this->menuModel->find($id);
        if ($menu === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('admin/menus/edit', [
            'menu'       => $menu,
            'parents'    => $this->getAvailableParents($id),
            'formAction' => site_url('admin/menus/' . $id),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $menu = $this->menuModel->find($id);
        if ($menu === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->validationRules())) {
            return $this->validationErrorRedirect($this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id') ?: null;

        if ($parentId !== null && in_array((int) $parentId, $this->getRestrictedParentIds($id), true)) {
            return $this->validationErrorRedirect(['parent_id' => '자기 자신 또는 하위 메뉴는 상위 메뉴로 선택할 수 없습니다.']);
        }

        $depth = $this->resolveDepth($parentId);
        if ($depth === null) {
            return $this->validationErrorRedirect(['parent_id' => '유효하지 않은 상위 메뉴입니다.']);
        }

        $this->menuModel->update($id, $this->buildMenuPayload($parentId, $depth));

        return redirect()->to('/admin/menus')->with('message', '메뉴가 수정되었습니다.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($this->menuModel->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->menuModel->delete($id);

        return redirect()->to('/admin/menus')->with('message', '메뉴가 삭제되었습니다. (하위 메뉴도 함께 삭제됩니다)');
    }

    public function toggleVisible(int $id): RedirectResponse
    {
        $menu = $this->menuModel->find($id);
        if ($menu === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->menuModel->update($id, ['is_visible' => $menu['is_visible'] ? 0 : 1]);

        return redirect()->to('/admin/menus')->with('message', '노출 여부가 변경되었습니다.');
    }

    /**
     * 같은 부모를 가진 형제 메뉴끼리 정렬 순서를 재배치한다. (드래그 앤 드롭)
     */
    public function reorder(): ResponseInterface
    {
        $parentId = $this->request->getPost('parent_id') ?: null;
        $ids      = $this->request->getPost('ids');

        if (! is_array($ids) || $ids === []) {
            return $this->jsonResponse(422, ['message' => '잘못된 요청입니다.']);
        }

        $ids = array_map('intval', $ids);

        $actualSiblingIds = array_map(
            static fn (array $menu): int => (int) $menu['id'],
            $this->menuModel->where('parent_id', $parentId)->findAll(),
        );

        $sortedActual    = $actualSiblingIds;
        $sortedRequested = $ids;
        sort($sortedActual);
        sort($sortedRequested);

        if ($sortedActual === [] || $sortedActual !== $sortedRequested) {
            return $this->jsonResponse(422, ['message' => '형제 메뉴 구성이 일치하지 않습니다.']);
        }

        $this->menuModel->transStart();
        foreach ($ids as $index => $id) {
            $this->menuModel->update($id, ['sort_order' => $index + 1]);
        }
        $this->menuModel->transComplete();

        if (! $this->menuModel->transStatus()) {
            return $this->jsonResponse(500, ['message' => '순서 변경에 실패했습니다.']);
        }

        return $this->jsonResponse(200, ['message' => '순서가 변경되었습니다.']);
    }

    /**
     * AJAX 응답 공통 처리. regenerate 옵션으로 CSRF 토큰이 매 요청마다 갱신되므로,
     * 다음 요청에서 사용할 최신 토큰을 함께 내려준다.
     */
    private function jsonResponse(int $statusCode, array $body): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($body + ['csrfToken' => csrf_hash()]);
    }

    /**
     * 검증 실패 시 공통으로 사용하는 리다이렉트.
     */
    private function validationErrorRedirect(array $errors): RedirectResponse
    {
        return redirect()->back()->withInput()->with('errors', $errors);
    }

    /**
     * 등록/수정에 공통으로 사용하는 메뉴 데이터.
     */
    private function buildMenuPayload(?string $parentId, int $depth): array
    {
        return [
            'parent_id'  => $parentId,
            'depth'      => $depth,
            'name'       => $this->request->getPost('name'),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_visible' => $this->request->getPost('is_visible') ? 1 : 0,
        ];
    }

    /**
     * @param list<array<string, mixed>> $menus
     *
     * @return list<array<string, mixed>>
     */
    private function buildTree(array $menus, ?int $parentId = null): array
    {
        $branch = [];

        foreach ($menus as $menu) {
            $menuParentId = $menu['parent_id'] !== null ? (int) $menu['parent_id'] : null;
            if ($menuParentId !== $parentId) {
                continue;
            }

            $children = $this->buildTree($menus, (int) $menu['id']);
            if ($children !== []) {
                $menu['children'] = $children;
            }
            $branch[] = $menu;
        }

        return $branch;
    }

    /**
     * 부모로 선택할 수 있는 메뉴 목록 (소분류는 하위를 가질 수 없으므로 제외).
     *
     * @return list<array<string, mixed>>
     */
    private function getAvailableParents(?int $excludeId = null): array
    {
        $menus = $this->menuModel
            ->where('depth <', self::MAX_DEPTH)
            ->orderBy('parent_id', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        if ($excludeId === null) {
            return $menus;
        }

        $restricted = $this->getRestrictedParentIds($excludeId);

        return array_values(array_filter(
            $menus,
            static fn (array $menu): bool => ! in_array((int) $menu['id'], $restricted, true),
        ));
    }

    /**
     * 자기 자신 및 모든 하위 메뉴 id (순환 참조 방지용).
     *
     * @return list<int>
     */
    private function getRestrictedParentIds(int $id): array
    {
        $descendantIds = $this->collectDescendantIds($this->menuModel->findAll(), $id);

        return [$id, ...$descendantIds];
    }

    /**
     * 평면 목록에서 특정 메뉴의 모든 하위 메뉴 id를 재귀적으로 수집한다.
     *
     * @param list<array<string, mixed>> $menus
     *
     * @return list<int>
     */
    private function collectDescendantIds(array $menus, int $parentId): array
    {
        $ids = [];

        foreach ($menus as $menu) {
            $menuParentId = $menu['parent_id'] !== null ? (int) $menu['parent_id'] : null;
            if ($menuParentId !== $parentId) {
                continue;
            }

            $childId = (int) $menu['id'];
            $ids[]   = $childId;

            array_push($ids, ...$this->collectDescendantIds($menus, $childId));
        }

        return $ids;
    }

    private function resolveDepth(?string $parentId): ?int
    {
        if ($parentId === null) {
            return 1;
        }

        $parent = $this->menuModel->find((int) $parentId);
        if ($parent === null || $parent['depth'] >= self::MAX_DEPTH) {
            return null;
        }

        return $parent['depth'] + 1;
    }

    private function validationRules(): array
    {
        return [
            'name'       => 'required|max_length[100]',
            'sort_order' => 'permit_empty|is_natural',
            'parent_id'  => 'permit_empty|is_natural_no_zero',
        ];
    }
}
