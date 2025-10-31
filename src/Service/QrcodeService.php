<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Service;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Renderer\Image\EpsImageBackEnd;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Autoconfigure(public: true)]
class QrcodeService
{
    private string $defaultFormat;

    private int $defaultSize = 300;

    private int $defaultMargin = 10;

    public function __construct(private readonly UrlGeneratorInterface $urlGenerator)
    {
        // 根据系统支持情况设置默认格式
        if (extension_loaded('gd')) {
            $this->defaultFormat = 'png';
        } elseif (extension_loaded('imagick')) {
            $this->defaultFormat = 'png';
        } else {
            $this->defaultFormat = 'svg';
        }
    }

    public function getImageUrl(string $url): string
    {
        return $this->urlGenerator->generate('qr_code_generate', [
            'data' => $url,
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * 生成二维码图像内容
     *
     * @param string $data    要编码的数据
     * @param array<string, mixed>  $options 选项，可包含 size, margin, format 等
     *
     * @return Response 包含二维码内容的响应对象
     */
    public function generateQrCode(string $data, array $options = []): Response
    {
        $size = $options['size'] ?? $this->defaultSize;
        $margin = $options['margin'] ?? $this->defaultMargin;
        $format = $options['format'] ?? $this->defaultFormat;

        // 根据格式和扩展支持情况选择渲染器
        if ('png' === $format && extension_loaded('gd')) {
            $renderer = new GDLibRenderer($size, $margin);
            $writer = new Writer($renderer);
            $content = $writer->writeString($data, 'UTF-8');
            $mimeType = 'image/png';
        } elseif ('png' === $format && extension_loaded('imagick')) {
            $renderer = new ImageRenderer(
                new RendererStyle($size, $margin),
                new ImagickImageBackEnd()
            );
            $writer = new Writer($renderer);
            $content = $writer->writeString($data, 'UTF-8');
            $mimeType = 'image/png';
        } elseif ('eps' === $format) {
            $renderer = new ImageRenderer(
                new RendererStyle($size, $margin),
                new EpsImageBackEnd()
            );
            $writer = new Writer($renderer);
            $content = $writer->writeString($data, 'UTF-8');
            $mimeType = 'application/postscript';
        } else {
            // 默认使用SVG
            $renderer = new ImageRenderer(
                new RendererStyle($size, $margin),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $content = $writer->writeString($data, 'UTF-8');
            $mimeType = 'image/svg+xml';
        }

        // 直接返回Response对象
        return new Response(
            $content,
            Response::HTTP_OK,
            ['Content-Type' => $mimeType]
        );
    }
}
