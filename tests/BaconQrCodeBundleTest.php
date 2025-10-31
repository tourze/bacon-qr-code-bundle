<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Tests;

use BaconQrCodeBundle\BaconQrCodeBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(BaconQrCodeBundle::class)]
#[RunTestsInSeparateProcesses]
final class BaconQrCodeBundleTest extends AbstractBundleTestCase
{
}
