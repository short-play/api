<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\Backend\Agreement\AgreementRequest;
use App\Service\AgreementService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Annotation\Scene;
use Psr\Http\Message\ResponseInterface;

class AgreementController extends AbstractController
{

    #[Inject]
    protected AgreementService $agreementService;

    /**
     * 获取详情
     * @param AgreementRequest $request
     * @return ResponseInterface
     */
    #[Scene('detail')]
    public function detail(AgreementRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $value = $this->agreementService->getAgreement($req['type'], $req['language']);
        return $this->response->success(compact('value'));
    }

    /**
     * 创建或修改协议
     * @param AgreementRequest $request
     * @return ResponseInterface
     */
    #[Scene('create')]
    public function create(AgreementRequest $request): ResponseInterface
    {
        $req = $request->validated();
        $this->agreementService->createOrUpdate($req['type'], $req['language'], $req['value']);
        return $this->response->success();
    }
}