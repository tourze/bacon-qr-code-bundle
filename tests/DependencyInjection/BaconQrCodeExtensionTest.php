<?php

namespace BaconQrCodeBundle\Tests\DependencyInjection;

use BaconQrCodeBundle\DependencyInjection\BaconQrCodeExtension;
use BaconQrCodeBundle\Service\QrcodeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BaconQrCodeExtensionTest extends TestCase
{
    private BaconQrCodeExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new BaconQrCodeExtension();
        $this->container = new ContainerBuilder();
        $this->extension->load([], $this->container);
    }

    public function testServicesConfigurationLoaded(): void
    {
        // 检查服务定义是否被加载
        $this->assertTrue($this->container->hasDefinition('BaconQrCodeBundle\Service\QrcodeService'));
        $this->assertTrue($this->container->hasDefinition('BaconQrCodeBundle\Controller\GenerateController'));
    }

    public function testQrcodeServiceAutoconfigured(): void
    {
        // 检查QrcodeService是否被正确配置
        $definition = $this->container->getDefinition('BaconQrCodeBundle\Service\QrcodeService');
        $this->assertTrue($definition->isAutoconfigured());
        $this->assertTrue($definition->isAutowired());
    }

    public function testControllerAutoconfigured(): void
    {
        // 检查Controller是否被正确配置
        $definition = $this->container->getDefinition('BaconQrCodeBundle\Controller\GenerateController');
        $this->assertTrue($definition->isAutoconfigured());
        $this->assertTrue($definition->isAutowired());
    }

    public function testServicesDependencyInjection(): void
    {
        // 检查QrcodeService的依赖注入配置
        $definition = $this->container->getDefinition('BaconQrCodeBundle\Service\QrcodeService');
        $this->assertEquals(QrcodeService::class, $definition->getClass());

        // 注意：根据服务配置和类定义，服务可能不是公开的，但可以通过Attribute的方式设置为公开
        // 我们不需要检查是否公开，因为这可能会根据配置方式而变化
    }
}
