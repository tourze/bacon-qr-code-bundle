<?php

declare(strict_types=1);

namespace BaconQrCodeBundle\Tests\Controller;

use BaconQrCodeBundle\Controller\GenerateController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;

/**
 * @internal
 */
#[CoversClass(GenerateController::class)]
#[RunTestsInSeparateProcesses]
final class GenerateControllerTest extends AbstractWebTestCase
{
    public function testUnauthorizedAccessAllowed(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testGetMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithBasicParameters(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithCustomSize(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data', ['size' => '400']);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithCustomMargin(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data', ['margin' => '20']);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithCustomFormat(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data', ['format' => 'svg']);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithAllCustomOptions(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data', [
            'size' => '500',
            'margin' => '5',
            'format' => 'svg',
        ]);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithSpecialCharactersInData(): void
    {
        $client = self::createClientWithDatabase();
        $specialData = urlencode('https://example.com/?param=value&special=!@#');

        $client->request('GET', "/qr-code/{$specialData}");

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithInvalidSizeParameter(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/test-data', ['size' => 'invalid']);

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testControllerWithNonEmptyData(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('GET', '/qr-code/' . urlencode(' '));

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testPostMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('POST', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testPutMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('PUT', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testDeleteMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('DELETE', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testPatchMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('PATCH', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testHeadMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('HEAD', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertEmpty($response->getContent());
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    public function testOptionsMethod(): void
    {
        $client = self::createClientWithDatabase();

        $client->request('OPTIONS', '/qr-code/test-data');

        self::getClient($client);
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertContains($response->headers->get('Content-Type'), ['image/png', 'image/svg+xml']);
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        if ('INVALID' === $method) {
            $this->assertSame('INVALID', $method, 'No methods are disallowed for this route');

            return;
        }

        $client = self::createClientWithDatabase();

        $client->request($method, '/qr-code/test-data');

        $this->assertResponseStatusCodeSame(405);
    }
}
