<?php

namespace BaconQrCodeBundle\Tests\Twig;

use BaconQrCodeBundle\Service\QrcodeService;
use BaconQrCodeBundle\Twig\QrcodeExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class QrcodeExtensionTest extends TestCase
{
    private QrcodeService&MockObject $qrcodeService;
    private QrcodeExtension $extension;

    protected function setUp(): void
    {
        $this->qrcodeService = $this->createMock(QrcodeService::class);
        $this->extension = new QrcodeExtension($this->qrcodeService);
    }

    public function testGetFunctions(): void
    {
        $functions = $this->extension->getFunctions();

        $this->assertCount(1, $functions);
        $this->assertInstanceOf(TwigFunction::class, $functions[0]);
        $this->assertEquals('qr_code_url', $functions[0]->getName());
    }

    public function testGetQrcodeUrl(): void
    {
        $testData = 'https://example.com';
        $expectedUrl = 'https://domain.com/qr-code/https://example.com';

        $this->qrcodeService
            ->expects($this->once())
            ->method('getImageUrl')
            ->with($testData)
            ->willReturn($expectedUrl);

        $result = $this->extension->getQrcodeUrl($testData);

        $this->assertEquals($expectedUrl, $result);
    }

    public function testGetQrcodeUrlWithEmptyString(): void
    {
        $testData = '';
        $expectedUrl = 'https://domain.com/qr-code/';

        $this->qrcodeService
            ->expects($this->once())
            ->method('getImageUrl')
            ->with($testData)
            ->willReturn($expectedUrl);

        $result = $this->extension->getQrcodeUrl($testData);

        $this->assertEquals($expectedUrl, $result);
    }

    public function testGetQrcodeUrlWithSpecialCharacters(): void
    {
        $testData = 'https://example.com/?param=value&special=!@#';
        $expectedUrl = 'https://domain.com/qr-code/' . urlencode($testData);

        $this->qrcodeService
            ->expects($this->once())
            ->method('getImageUrl')
            ->with($testData)
            ->willReturn($expectedUrl);

        $result = $this->extension->getQrcodeUrl($testData);

        $this->assertEquals($expectedUrl, $result);
    }
}
