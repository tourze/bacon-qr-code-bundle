<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Twig;

use BaconQrCodeBundle\Service\QrcodeService;
use Twig\Attribute\AsTwigFunction;

readonly class QrcodeExtension
{
    public function __construct(
        private QrcodeService $qrcodeService,
    ) {
    }

    /**
     * 获取二维码URL
     *
     * @param string $data 要编码的数据
     *
     * @return string 二维码图片URL
     */
    #[AsTwigFunction(name: 'qr_code_url')]
    public function getQrcodeUrl(string $data): string
    {
        return $this->qrcodeService->getImageUrl($data);
    }
}
