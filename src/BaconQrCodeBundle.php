<?php

namespace BaconQrCodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class BaconQrCodeBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle::class => ['all' => true],
        ];
    }
}
