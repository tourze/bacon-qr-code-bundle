<?php

namespace BaconQrCodeBundle\Tests\Integration;

use BaconQrCodeBundle\BaconQrCodeBundle;
use BaconQrCodeBundle\Twig\QrcodeExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;
use Twig\Environment;

class TwigExtensionIntegrationTest extends KernelTestCase
{
    /** @phpstan-ignore-next-line missingType.iterableValue */
    protected static function createKernel(array $options = []): KernelInterface
    {
        $env = $options['environment'] ?? $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'test';
        $debug = $options['debug'] ?? $_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? true;

        return new IntegrationTestKernel($env, $debug, [
            BaconQrCodeBundle::class => ['all' => true],
        ]);
    }

    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testTwigExtensionRegistered(): void
    {
        /** @var \Symfony\Component\DependencyInjection\Container $container */
        $container = self::getContainer();

        // 测试 Twig 扩展是否注册
        $this->assertTrue($container->has('BaconQrCodeBundle\Twig\QrcodeExtension'));

        $extension = $container->get('BaconQrCodeBundle\Twig\QrcodeExtension');
        $this->assertInstanceOf(QrcodeExtension::class, $extension);
    }

    public function testTwigFunctionAvailable(): void
    {
        /** @var \Symfony\Component\DependencyInjection\Container $container */
        $container = self::getContainer();

        /** @var Environment $twig */
        $twig = $container->get('twig');

        // 验证 qr_code_url 函数是否可用
        $functions = $twig->getFunctions();
        $functionNames = array_map(fn($func) => $func->getName(), $functions);

        $this->assertContains('qr_code_url', $functionNames);
    }

    public function testTwigFunctionExecution(): void
    {
        /** @var \Symfony\Component\DependencyInjection\Container $container */
        $container = self::getContainer();

        /** @var Environment $twig */
        $twig = $container->get('twig');

        // 创建简单的模板并测试函数执行
        $template = $twig->createTemplate('{{ qr_code_url("https://example.com") }}');
        $result = $template->render();

        // 验证结果包含预期的URL结构
        $this->assertStringContainsString('/qr-code/', $result);
        $this->assertStringContainsString('https://example.com', $result);
    }

    public function testTwigFunctionWithDifferentData(): void
    {
        /** @var \Symfony\Component\DependencyInjection\Container $container */
        $container = self::getContainer();

        /** @var Environment $twig */
        $twig = $container->get('twig');

        $testCases = [
            'Simple text',
            'https://github.com',
            'mailto:test@example.com',
            '中文测试',
        ];

        foreach ($testCases as $testData) {
            $template = $twig->createTemplate('{{ qr_code_url(data) }}');
            $result = $template->render(['data' => $testData]);

            $this->assertStringContainsString('/qr-code/', $result);
            $this->assertNotEmpty($result);
        }
    }
}
