<?php

namespace App\Controllers\Admin;

use App\Models\Category;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class CategoryController extends BaseController
{
    private Category $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
    }

    public function index(): string
    {
        $categories = $this->categoryModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return view('admin/categories/index', [
            'categories' => $categories,
        ]);
    }

    public function create(): string
    {
        return view('admin/categories/create', [
            'category'   => null,
            'formAction' => site_url('admin/categories'),
        ]);
    }

    public function store(): RedirectResponse
    {
        if (! $this->validate($this->validationRules())) {
            return $this->validationErrorRedirect($this->validator->getErrors());
        }

        $this->categoryModel->insert($this->buildCategoryPayload());

        return redirect()->to('/admin/categories')->with('message', '카테고리가 등록되었습니다.');
    }

    public function edit(int $id): string
    {
        $category = $this->categoryModel->find($id);
        if ($category === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('admin/categories/edit', [
            'category'   => $category,
            'formAction' => site_url('admin/categories/' . $id),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        if ($this->categoryModel->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (! $this->validate($this->validationRules($id))) {
            return $this->validationErrorRedirect($this->validator->getErrors());
        }

        $this->categoryModel->update($id, $this->buildCategoryPayload());

        return redirect()->to('/admin/categories')->with('message', '카테고리가 수정되었습니다.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($this->categoryModel->find($id) === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $this->categoryModel->delete($id);

        return redirect()->to('/admin/categories')->with('message', '카테고리가 삭제되었습니다.');
    }

    public function toggleVisible(int $id): RedirectResponse
    {
        return $this->updateVisibility($this->categoryModel, $id, '/admin/categories', '노출 여부가 변경되었습니다.');
    }

    /**
     * 카테고리는 계층이 없어 전체가 하나의 그룹이므로 스코프 없이 정렬한다. (드래그 앤 드롭)
     */
    public function reorder(): ResponseInterface
    {
        return $this->handleReorder($this->categoryModel, [], '카테고리 구성이 일치하지 않습니다.');
    }

    /**
     * 등록/수정에 공통으로 사용하는 카테고리 데이터.
     */
    private function buildCategoryPayload(): array
    {
        return [
            'name'       => $this->request->getPost('name'),
            'sort_order' => (int) $this->request->getPost('sort_order'),
            'is_visible' => $this->request->getPost('is_visible') ? 1 : 0,
        ];
    }

    private function validationRules(?int $excludeId = null): array
    {
        $uniqueRule = 'is_unique[categories.name'
            . ($excludeId !== null ? ',id,' . $excludeId : '')
            . ']';

        return [
            'name'       => "required|max_length[100]|{$uniqueRule}",
            'sort_order' => 'permit_empty|is_natural',
        ];
    }
}
