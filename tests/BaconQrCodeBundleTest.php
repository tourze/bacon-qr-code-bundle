<?php

namespace BaconQrCodeBundle\Tests;

use BaconQrCodeBundle\BaconQrCodeBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BaconQrCodeBundleTest extends TestCase
{
    public function testInstanceOfBundle(): void
    {
        $bundle = new BaconQrCodeBundle();

        // 测试Bundle类的继承关系
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testGetPath(): void
    {
        $bundle = new BaconQrCodeBundle();

        // 测试路径解析
        $path = $bundle->getPath();

        // 验证路径是否正确
        $this->assertDirectoryExists($path);

        // 检查路径是否包含 bacon-qr-code-bundle，而不是检查结尾
        $this->assertStringContainsString('bacon-qr-code-bundle', $path);
    }
}
