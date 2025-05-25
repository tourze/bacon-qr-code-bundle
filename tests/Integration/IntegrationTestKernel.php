<?php

namespace BaconQrCodeBundle\Tests\Integration;

use BaconQrCodeBundle\BaconQrCodeBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;

class IntegrationTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new RoutingAutoLoaderBundle(),
            new BaconQrCodeBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'router' => [
                'utf8' => true,
            ],
            'secret' => 'test',
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => [
                'log' => true,
            ],
            'validation' => [
                'email_validation_mode' => 'html5',
            ],
            'uid' => [
                'default_uuid_version' => 7,
                'time_based_uuid_version' => 7,
            ],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // 路由会通过 RoutingAutoLoaderBundle 自动加载控制器的路由注解
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/bacon_qr_code_bundle_tests/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/bacon_qr_code_bundle_tests/log';
    }
}
