<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Controller;

use BaconQrCodeBundle\Service\QrcodeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GenerateController extends AbstractController
{
    public function __construct(
        private readonly QrcodeService $qrcodeService,
    ) {
    }

    #[Route('/qr-code/{data}', name: 'qr_code_generate', requirements: ['data' => '[\\w\\W]+'])]
    public function renderCode(string $data, Request $request): Response
    {
        // 从请求参数获取配置选项
        $options = [
            'size' => $request->query->getInt('size', 300),
            'margin' => $request->query->getInt('margin', 1),
            'format' => $request->query->get('format'),
        ];

        // 过滤掉未设置的选项
        $options = array_filter($options, fn ($value) => null !== $value);

        // 生成二维码并直接返回Response对象
        return $this->qrcodeService->generateQrCode($data, $options);
    }
}
