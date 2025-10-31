<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Tests\Service;

use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCodeBundle\Service\QrcodeService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(QrcodeService::class)]
#[RunTestsInSeparateProcesses]
final class QrcodeServiceTest extends AbstractIntegrationTestCase
{
    private QrcodeService $qrcodeService;

    protected function onSetUp(): void
    {
        $this->qrcodeService = self::getService(QrcodeService::class);
    }

    public function testGetImageUrlWithValidData(): void
    {
        // 执行测试
        $result = $this->qrcodeService->getImageUrl('https://example.com');

        // 断言结果包含基本路径
        $this->assertStringContainsString('qr-code/https://example.com', $result);
    }

    public function testGetImageUrlWithEmptyString(): void
    {
        // 空字符串会导致路由参数验证失败，这是预期的行为
        $this->expectException(InvalidParameterException::class);

        // 执行测试
        $this->qrcodeService->getImageUrl('');
    }

    public function testGetImageUrlWithSpecialCharacters(): void
    {
        $specialUrl = 'https://example.com/?param=value&special=!@#';

        // 执行测试
        $result = $this->qrcodeService->getImageUrl($specialUrl);

        // 断言结果
        $this->assertStringContainsString('qr-code/', $result);
    }

    public function testGenerateQrCodeWithDefaultOptions(): void
    {
        // 此测试依赖于系统环境，需要基于实际环境进行测试
        $data = 'https://example.com';

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data);

        // 断言响应类型和状态码
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        // 断言内容不为空
        $this->assertNotEmpty($response->getContent());

        // 断言内容类型
        $contentType = $response->headers->get('Content-Type');
        $validContentTypes = ['image/png', 'image/svg+xml', 'application/postscript'];
        $this->assertContains($contentType, $validContentTypes, '内容类型应为PNG、SVG或EPS');
    }

    public function testGenerateQrCodeWithCustomSize(): void
    {
        $data = 'https://example.com';
        $options = ['size' => 400];

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data, $options);

        // 断言基本属性
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());

        // 注意：我们无法直接验证图像大小，因为这需要解析图像内容
        // 我们只能验证响应有效
    }

    public function testGenerateQrCodeWithCustomMargin(): void
    {
        $data = 'https://example.com';
        $options = ['margin' => 20];

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data, $options);

        // 断言
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }

    public function testGenerateQrCodeWithSvgFormat(): void
    {
        $data = 'https://example.com';
        $options = ['format' => 'svg'];

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data, $options);

        // 断言
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());

        // 验证内容包含SVG标记
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function testGenerateQrCodeWithEpsFormat(): void
    {
        $data = 'https://example.com';
        $options = ['format' => 'eps'];

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data, $options);

        // 断言
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('application/postscript', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
    }

    public function testGenerateQrCodeWithCombinedOptions(): void
    {
        $data = 'https://example.com';
        $options = [
            'size' => 500,
            'margin' => 5,
            'format' => 'svg',
        ];

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data, $options);

        // 断言
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('image/svg+xml', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function testGenerateQrCodeWithEmptyDataShouldThrowException(): void
    {
        // 测试空数据抛出异常
        $data = '';

        // 期望抛出异常
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Found empty contents');

        // 执行测试
        $this->qrcodeService->generateQrCode($data);
    }

    public function testGenerateQrCodeWithLongData(): void
    {
        // 测试较长的数据
        $data = str_repeat('https://example.com/', 50);

        // 执行测试
        $response = $this->qrcodeService->generateQrCode($data);

        // 断言
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($response->getContent());
    }
}
