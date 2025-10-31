<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Tests\Service;

use BaconQrCodeBundle\Service\AttributeControllerLoader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    private AttributeControllerLoader $loader;

    protected function onSetUp(): void
    {
        $this->loader = self::getService(AttributeControllerLoader::class);
    }

    public function testInstanceOfLoader(): void
    {
        // 测试继承关系
        $this->assertInstanceOf(Loader::class, $this->loader);
        $this->assertInstanceOf(RoutingAutoLoaderInterface::class, $this->loader);
    }

    public function testLoadMethod(): void
    {
        // 测试 load 方法返回路由集合
        $result = $this->loader->load('any-resource');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testLoadWithResourceAndType(): void
    {
        // 测试带资源和类型参数的 load 方法
        $result = $this->loader->load('test-resource', 'test-type');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupportsMethod(): void
    {
        // 测试 supports 方法始终返回 false
        $this->assertFalse($this->loader->supports('any-resource'));
        $this->assertFalse($this->loader->supports('any-resource', 'any-type'));
        $this->assertFalse($this->loader->supports(null));
        $this->assertFalse($this->loader->supports(null, null));
    }

    public function testAutoloadMethod(): void
    {
        // 测试 autoload 方法返回路由集合
        $result = $this->loader->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testLoadedRoutesNotEmpty(): void
    {
        // 测试加载的路由集合不为空
        $collection = $this->loader->autoload();

        // GenerateController 应该至少包含一个路由
        $this->assertGreaterThan(0, $collection->count(), '应该至少包含一个从 GenerateController 加载的路由');
    }

    public function testRoutesAreConsistent(): void
    {
        // 测试多次调用返回一致的路由
        $collection1 = $this->loader->autoload();
        $collection2 = $this->loader->autoload();

        // 路由数量应该相同
        $this->assertEquals($collection1->count(), $collection2->count());

        // 路由名称应该相同
        $routes1 = array_keys($collection1->all());
        $routes2 = array_keys($collection2->all());
        $this->assertEquals($routes1, $routes2);
    }

    public function testSpecificRouteExists(): void
    {
        // 测试特定路由是否存在
        $collection = $this->loader->autoload();
        $routes = $collection->all();

        // 检查是否包含二维码生成路由
        $this->assertArrayHasKey('qr_code_generate', $routes, '应该包含 qr_code_generate 路由');

        // 验证路由路径
        $route = $routes['qr_code_generate'];
        $this->assertEquals('/qr-code/{data}', $route->getPath());
    }

    public function testRouteRequirements(): void
    {
        // 测试路由要求
        $collection = $this->loader->autoload();
        $routes = $collection->all();

        $route = $routes['qr_code_generate'];
        $requirements = $route->getRequirements();

        // 验证 data 参数的正则表达式要求
        $this->assertArrayHasKey('data', $requirements);
        $this->assertEquals('[\w\W]+', $requirements['data']);
    }

    public function testRouteMethods(): void
    {
        // 测试路由HTTP方法
        $collection = $this->loader->autoload();
        $routes = $collection->all();

        $route = $routes['qr_code_generate'];
        $methods = $route->getMethods();

        // 如果没有指定方法，则支持所有方法
        $this->assertTrue([] === $methods || in_array('GET', $methods, true), '应该支持 GET 方法');
    }
}
