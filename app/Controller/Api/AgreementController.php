<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Constants\Enum\Agreement;
use App\Controller\AbstractController;
use App\Service\AgreementService;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class AgreementController extends AbstractController
{

    #[Inject]
    protected AgreementService $agreementService;

    /**
     * 用户协议
     * @return ResponseInterface
     */
    public function userAgreement(): ResponseInterface
    {
        $value = $this->agreementService->getAgreement(
            Agreement::UserAgreement->value, $this->getLanguage()
        );
        return $this->response->success(compact('value'));
    }

    /**
     * 隐私协议
     * @return ResponseInterface
     */
    public function privacyPolicy(): ResponseInterface
    {
        $value = $this->agreementService->getAgreement(
            Agreement::PrivacyAgreement->value, $this->getLanguage()
        );
        return $this->response->success(compact('value'));
    }
}