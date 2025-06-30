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

    #[Route(path: '/qr-code/{data}', name: 'qr_code_generate', requirements: ['data' => '[\\w\\W]+'])]
    public function __invoke(string $data, Request $request): Response
    {
        // 从请求参数获取配置选项，使用 filter() 代替 getInt() 以解决废弃警告
        $options = [
            'size' => $request->query->filter('size', 300, FILTER_VALIDATE_INT, [
                'options' => ['default' => 300],
                'flags' => FILTER_NULL_ON_FAILURE,
            ]),
            'margin' => $request->query->filter('margin', 1, FILTER_VALIDATE_INT, [
                'options' => ['default' => 1],
                'flags' => FILTER_NULL_ON_FAILURE,
            ]),
            'format' => $request->query->get('format'),
        ];

        // 过滤掉未设置的选项
        $options = array_filter($options, fn ($value) => null !== $value);

        // 生成二维码并直接返回Response对象
        return $this->qrcodeService->generateQrCode($data, $options);
    }
}
