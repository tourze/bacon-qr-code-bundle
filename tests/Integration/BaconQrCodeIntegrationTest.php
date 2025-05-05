<?php

namespace BaconQrCodeBundle\Tests\Integration;

use BaconQrCodeBundle\Service\QrcodeService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class BaconQrCodeIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testServiceWiring(): void
    {
        /** @var ContainerInterface $container */
        $container = self::getContainer();

        // 测试服务是否可以从容器中获取
        $this->assertTrue($container->has('BaconQrCodeBundle\Service\QrcodeService'));
        $service = $container->get('BaconQrCodeBundle\Service\QrcodeService');
        $this->assertInstanceOf(QrcodeService::class, $service);
    }

    public function testQrcodeServiceGenerateQrCode(): void
    {
        /** @var ContainerInterface $container */
        $container = self::getContainer();

        /** @var QrcodeService $service */
        $service = $container->get('BaconQrCodeBundle\Service\QrcodeService');

        // 测试生成二维码
        $response = $service->generateQrCode('https://example.com');

        // 基本断言
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());

        // 内容类型检查
        $contentType = $response->headers->get('Content-Type');
        $validContentTypes = ['image/png', 'image/svg+xml', 'application/postscript'];
        $this->assertContains($contentType, $validContentTypes);
    }

    public function testQrcodeServiceGetImageUrl(): void
    {
        /** @var ContainerInterface $container */
        $container = self::getContainer();

        /** @var QrcodeService $service */
        $service = $container->get('BaconQrCodeBundle\Service\QrcodeService');

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $container->get('router');

        // 生成基准URL用于比较
        $expectedUrl = $urlGenerator->generate(
            'qr_code_generate',
            ['data' => 'https://example.com'],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // 测试URL生成功能
        $generatedUrl = $service->getImageUrl('https://example.com');

        // 断言结果
        $this->assertEquals($expectedUrl, $generatedUrl);
    }

    public function testQrcodeServiceWithDifferentFormats(): void
    {
        /** @var ContainerInterface $container */
        $container = self::getContainer();

        /** @var QrcodeService $service */
        $service = $container->get('BaconQrCodeBundle\Service\QrcodeService');

        // 测试不同格式
        $formats = ['svg', 'eps'];
        $mimeTypes = ['image/svg+xml', 'application/postscript'];

        foreach ($formats as $index => $format) {
            $response = $service->generateQrCode('https://example.com', ['format' => $format]);

            $this->assertInstanceOf(Response::class, $response);
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertEquals($mimeTypes[$index], $response->headers->get('Content-Type'));
            $this->assertNotEmpty($response->getContent());
        }
    }
}
