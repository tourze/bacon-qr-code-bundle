<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Tests\DependencyInjection;

use BaconQrCodeBundle\DependencyInjection\BaconQrCodeExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(BaconQrCodeExtension::class)]
final class BaconQrCodeExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private BaconQrCodeExtension $extension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extension = new BaconQrCodeExtension();
    }

    protected function provideServiceDirectories(): iterable
    {
        yield from parent::provideServiceDirectories();
        yield 'Twig';
    }

    public function testGetAlias(): void
    {
        $this->assertEquals('bacon_qr_code', $this->extension->getAlias());
    }
}
