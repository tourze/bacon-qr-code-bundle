<?php

namespace BaconQrCodeBundle\Tests\Service;

use BaconQrCode\Exception\InvalidArgumentException;
use BaconQrCodeBundle\Service\QrcodeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class QrcodeServiceTest extends TestCase
{
    private MockObject|UrlGeneratorInterface $urlGenerator;
    private QrcodeService $qrcodeService;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->qrcodeService = new QrcodeService($this->urlGenerator);
    }

    public function testGetImageUrl_withValidData(): void
    {
        // 设置模拟对象行为
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'qr_code_generate',
                ['data' => 'https://example.com'],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn('https://domain.com/qr-code/https://example.com');

        // 执行测试
        $result = $this->qrcodeService->getImageUrl('https://example.com');

        // 断言结果
        $this->assertEquals('https://domain.com/qr-code/https://example.com', $result);
    }

    public function testGetImageUrl_withEmptyString(): void
    {
        // 设置模拟对象行为
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'qr_code_generate',
                ['data' => ''],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn('https://domain.com/qr-code/');

        // 执行测试
        $result = $this->qrcodeService->getImageUrl('');

        // 断言结果
        $this->assertEquals('https://domain.com/qr-code/', $result);
    }

    public function testGetImageUrl_withSpecialCharacters(): void
    {
        $specialUrl = 'https://example.com/?param=value&special=!@#';

        // 设置模拟对象行为
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(
                'qr_code_generate',
                ['data' => $specialUrl],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            ->willReturn('https://domain.com/qr-code/' . urlencode($specialUrl));

        // 执行测试
        $result = $this->qrcodeService->getImageUrl($specialUrl);

        // 断言结果
        $this->assertEquals('https://domain.com/qr-code/' . urlencode($specialUrl), $result);
    }

    public function testGenerateQrCode_withDefaultOptions(): void
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
        $this->assertContains($contentType, $validContentTypes, "内容类型应为PNG、SVG或EPS");
    }

    public function testGenerateQrCode_withCustomSize(): void
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

    public function testGenerateQrCode_withCustomMargin(): void
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

    public function testGenerateQrCode_withSvgFormat(): void
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

    public function testGenerateQrCode_withEpsFormat(): void
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

    public function testGenerateQrCode_withCombinedOptions(): void
    {
        $data = 'https://example.com';
        $options = [
            'size' => 500,
            'margin' => 5,
            'format' => 'svg'
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

    public function testGenerateQrCode_withEmptyData_shouldThrowException(): void
    {
        // 测试空数据抛出异常
        $data = '';

        // 期望抛出异常
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Found empty contents');

        // 执行测试
        $this->qrcodeService->generateQrCode($data);
    }

    public function testGenerateQrCode_withLongData(): void
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
