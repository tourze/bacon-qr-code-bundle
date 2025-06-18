<?php

namespace BaconQrCodeBundle\Tests\Controller;

use BaconQrCodeBundle\Controller\GenerateController;
use BaconQrCodeBundle\Service\QrcodeService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GenerateControllerTest extends TestCase
{
    private MockObject|QrcodeService $qrcodeService;
    private GenerateController $controller;

    protected function setUp(): void
    {
        $this->qrcodeService = $this->createMock(QrcodeService::class);
        $this->controller = new GenerateController($this->qrcodeService);
    }

    public function testRenderCode_withBasicParameters(): void
    {
        // 准备模拟请求对象
        $request = new Request();

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                'test-data',
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 300
                        && isset($options['margin']) && $options['margin'] === 1;
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke('test-data', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withCustomSize(): void
    {
        // 准备带有自定义尺寸的请求
        $request = new Request(['size' => '400']);

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                'test-data',
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 400
                        && isset($options['margin']) && $options['margin'] === 1;
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke('test-data', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withCustomMargin(): void
    {
        // 准备带有自定义边距的请求
        $request = new Request(['margin' => '20']);

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                'test-data',
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 300
                        && isset($options['margin']) && $options['margin'] === 20;
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke('test-data', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withCustomFormat(): void
    {
        // 准备带有自定义格式的请求
        $request = new Request(['format' => 'svg']);

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                'test-data',
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 300
                        && isset($options['margin']) && $options['margin'] === 1
                        && isset($options['format']) && $options['format'] === 'svg';
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke('test-data', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withAllCustomOptions(): void
    {
        // 准备带有所有自定义选项的请求
        $request = new Request([
            'size' => '500',
            'margin' => '5',
            'format' => 'svg'
        ]);

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                'test-data',
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 500
                        && isset($options['margin']) && $options['margin'] === 5
                        && isset($options['format']) && $options['format'] === 'svg';
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke('test-data', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withSpecialCharactersInData(): void
    {
        // 准备模拟请求对象
        $request = new Request();
        $specialData = 'https://example.com/?param=value&special=!@#';

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                $specialData,
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 300
                        && isset($options['margin']) && $options['margin'] === 1;
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke($specialData, $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withInvalidSizeParameter(): void
    {
        // 准备带有无效尺寸（非数字）的请求
        $request = new Request(['size' => 'invalid']);

        // 使用 filter() 方法后，无效值将返回默认值300，而非0
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                'test-data',
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 300
                        && isset($options['margin']) && $options['margin'] === 1;
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke('test-data', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }

    public function testRenderCode_withNonEmptyData(): void
    {
        // 使用非空数据替代空字符串，因为底层库不接受空字符串
        $request = new Request();

        // 设置服务模拟对象的期望行为
        $expectedResponse = new Response('QR Code Content');
        $this->qrcodeService
            ->expects($this->once())
            ->method('generateQrCode')
            ->with(
                ' ',  // 使用空格替代空字符串
                $this->callback(function (array $options) {
                    return isset($options['size']) && $options['size'] === 300
                        && isset($options['margin']) && $options['margin'] === 1;
                })
            )
            ->willReturn($expectedResponse);

        // 执行测试
        $response = $this->controller->__invoke(' ', $request);

        // 断言结果
        $this->assertSame($expectedResponse, $response);
    }
}
