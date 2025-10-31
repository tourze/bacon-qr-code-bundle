<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class BaconQrCodeExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
