<?php

namespace BaconQrCodeBundle\Twig;

use BaconQrCodeBundle\Service\QrcodeService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class QrcodeExtension extends AbstractExtension
{
    public function __construct(
        private readonly QrcodeService $qrcodeService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('qr_code_url', $this->getQrcodeUrl(...)),
        ];
    }

    /**
     * 获取二维码URL
     *
     * @param string $data 要编码的数据
     *
     * @return string 二维码图片URL
     */
    public function getQrcodeUrl(string $data): string
    {
        return $this->qrcodeService->getImageUrl($data);
    }
}
