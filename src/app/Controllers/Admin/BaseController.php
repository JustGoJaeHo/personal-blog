<?php

namespace App\Controllers\Admin;

use App\Models\Concerns\Sortable;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Model;

/**
 * 목록형 관리 리소스(메뉴, 카테고리 등) 컨트롤러가 공통으로 쓰는 동작을 모아둔다.
 */
abstract class BaseController extends \App\Controllers\BaseController
{
    /**
     * 검증 실패 시 공통으로 사용하는 리다이렉트.
     */
    protected function validationErrorRedirect(array $errors): RedirectResponse
    {
        return redirect()->back()->withInput()->with('errors', $errors);
    }

    /**
     * is_visible 컬럼을 가진 모델의 노출 여부를 반전시키고 목록으로 리다이렉트한다.
     */
    protected function updateVisibility(Model $model, int $id, string $redirectTo, string $successMessage): RedirectResponse
    {
        $row = $model->find($id);
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $model->update($id, ['is_visible' => $row['is_visible'] ? 0 : 1]);

        return redirect()->to($redirectTo)->with('message', $successMessage);
    }

    /**
     * 드래그 앤 드롭 정렬 요청(ids[])을 검증/반영하고 JSON으로 응답한다.
     *
     * @param array<string, mixed> $scope 같은 그룹(예: 형제 메뉴)으로 취급할 조건. 계층이 없는 리소스는 빈 배열.
     */
    protected function handleReorder(Sortable $model, array $scope, string $mismatchMessage): ResponseInterface
    {
        $ids = $this->request->getPost('ids');
        if (! is_array($ids) || $ids === []) {
            return $this->jsonResponse(422, ['message' => '잘못된 요청입니다.']);
        }

        $result = $model->reorderWithinScope(array_map('intval', $ids), $scope);

        if ($result === null) {
            return $this->jsonResponse(422, ['message' => $mismatchMessage]);
        }
        if ($result === false) {
            return $this->jsonResponse(500, ['message' => '순서 변경에 실패했습니다.']);
        }

        return $this->jsonResponse(200, ['message' => '순서가 변경되었습니다.']);
    }

    /**
     * AJAX 응답 공통 처리. regenerate 옵션으로 CSRF 토큰이 매 요청마다 갱신되므로,
     * 다음 요청에서 쓸 최신 토큰을 응답에 함께 실어 보낸다.
     */
    protected function jsonResponse(int $statusCode, array $body): ResponseInterface
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($body + ['csrfToken' => csrf_hash()]);
    }
}
