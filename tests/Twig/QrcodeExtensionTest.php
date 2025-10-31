<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Tests\Twig;

use BaconQrCodeBundle\Service\QrcodeService;
use BaconQrCodeBundle\Twig\QrcodeExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(QrcodeExtension::class)]
#[RunTestsInSeparateProcesses]
final class QrcodeExtensionTest extends AbstractIntegrationTestCase
{
    private QrcodeService $qrcodeService;

    private QrcodeExtension $extension;

    protected function onSetUp(): void
    {
        // 创建 QrcodeService 的匿名类实现
        /** @var UrlGeneratorInterface $router */
        $router = self::getContainer()->get('router');
        $this->qrcodeService = new class($router) extends QrcodeService {
            public function getImageUrl(string $url): string
            {
                // 返回模拟的URL，不调用父类方法
                if ('' === $url) {
                    return 'https://domain.com/qr-code/';
                }
                if ('https://example.com/?param=value&special=!@#' === $url) {
                    return 'https://domain.com/qr-code/' . urlencode($url);
                }

                return 'https://domain.com/qr-code/' . $url;
            }
        };
        // 将服务注入到容器中
        self::getContainer()->set(QrcodeService::class, $this->qrcodeService);
        // 从容器中获取 QrcodeExtension 实例
        $this->extension = self::getService(QrcodeExtension::class);
    }

    public function testGetQrcodeUrl(): void
    {
        $testData = 'https://example.com';
        $expectedUrl = 'https://domain.com/qr-code/https://example.com';

        // 使用匿名类的固定返回逻辑

        $result = $this->extension->getQrcodeUrl($testData);

        $this->assertEquals($expectedUrl, $result);
    }

    public function testGetQrcodeUrlWithEmptyString(): void
    {
        $testData = '';
        $expectedUrl = 'https://domain.com/qr-code/';

        // 使用匿名类的固定返回逻辑

        $result = $this->extension->getQrcodeUrl($testData);

        $this->assertEquals($expectedUrl, $result);
    }

    public function testGetQrcodeUrlWithSpecialCharacters(): void
    {
        $testData = 'https://example.com/?param=value&special=!@#';
        $expectedUrl = 'https://domain.com/qr-code/' . urlencode($testData);

        // 使用匿名类的固定返回逻辑

        $result = $this->extension->getQrcodeUrl($testData);

        $this->assertEquals($expectedUrl, $result);
    }
}
