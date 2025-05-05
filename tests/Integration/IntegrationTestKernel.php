<?php

namespace BaconQrCodeBundle\Tests\Integration;

use BaconQrCodeBundle\BaconQrCodeBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

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
            'validation' => [
                'email_validation_mode' => 'html5',
            ],
            'php_errors' => [
                'log' => true,
            ],
            'uid' => [
                'default_uuid_version' => 7,
                'time_based_uuid_version' => 7,
            ],
        ]);
    }

    protected function configureRoutes($routes): void
    {
        if ($routes instanceof RoutingConfigurator) {
            $routes->import('@BaconQrCodeBundle/Resources/config/routes.yaml');
        } else {
            $routes->import('@BaconQrCodeBundle/Resources/config/routes.yaml', '/', 'yaml');
        }
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
