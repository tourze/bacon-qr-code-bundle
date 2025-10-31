# Bacon QR Code Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/bacon-qr-code-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/bacon-qr-code-bundle)
[![License](https://img.shields.io/github/license/tourze/php-monorepo.svg?style=flat-square)](https://github.com/tourze/php-monorepo/blob/main/LICENSE)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/bacon-qr-code-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/bacon-qr-code-bundle)

A Symfony bundle for [bacon/bacon-qr-code](https://github.com/Bacon/BaconQrCode) library that provides QR code generation capabilities in your Symfony application.

## Features

- Easy integration with Symfony applications
- Multiple output formats support (PNG, SVG, EPS)
- Customizable QR code size and margin
- Simple URL generation for QR codes
- Automatic detection of available image libraries (GD, Imagick)
- Twig function for easy template integration

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- ext-filter PHP extension
- One of the following image libraries (optional, for PNG support):
  - GD extension (ext-gd)
  - ImageMagick extension (ext-imagick)

## Installation

```bash
composer require tourze/bacon-qr-code-bundle
```

Register the bundle in your `config/bundles.php`:

```php
<?php

return [
    // ...
    BaconQrCodeBundle\BaconQrCodeBundle::class => ['all' => true],
];
```

Import routes in your `config/routes.yaml`:

```yaml
bacon_qr_code:
    resource: '@BaconQrCodeBundle/Resources/config/routes.yaml'
```

## Quick Start

### Generate QR Code URL

```php
<?php

use BaconQrCodeBundle\Service\QrcodeService;

class YourController
{
    public function example(QrcodeService $qrcodeService)
    {
        // Generate URL for QR code image
        $qrCodeUrl = $qrcodeService->getImageUrl('https://example.com');

        // Use this URL in your templates
        return $this->render('your_template.html.twig', [
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }
}
```

### Display QR Code in Twig Template

```twig
{# Using the controller variable #}
<img src="{{ qrCodeUrl }}" alt="QR Code">

{# Using the Twig function #}
<img src="{{ qr_code_url('https://example.com') }}" alt="QR Code">

{# With custom parameters #}
<img src="{{ path('qr_code_generate', {
    'data': 'https://example.com',
    'size': 400,
    'margin': 0,
    'format': 'svg'
}) }}" alt="QR Code">
```

### Direct QR Code Generation

```php
<?php

use BaconQrCodeBundle\Service\QrcodeService;
use Symfony\Component\HttpFoundation\Response;

class YourController
{
    public function generateQrCode(QrcodeService $qrcodeService): Response
    {
        $options = [
            'size' => 300,     // Size in pixels
            'margin' => 10,    // Margin in pixels
            'format' => 'png', // Format: 'png', 'svg', 'eps'
        ];

        // Return Response object with QR code content
        return $qrcodeService->generateQrCode('https://example.com', $options);
    }
}
```

## Twig Functions

### qr_code_url

Generate a QR code URL directly in your templates:

```twig
{# Basic usage #}
<img src="{{ qr_code_url('https://example.com') }}" alt="QR Code">

{# With different content types #}
<img src="{{ qr_code_url('mailto:contact@example.com') }}" alt="Email QR Code">
<img src="{{ qr_code_url('tel:+1234567890') }}" alt="Phone QR Code">
<img src="{{ qr_code_url('Simple text content') }}" alt="Text QR Code">

{# Using variables #}
<img src="{{ qr_code_url(user.email) }}" alt="User Email QR Code">
```

## Available Options

When generating QR codes, you can customize the following options:

| Option   | Description                                          | Default Value                       |
|----------|------------------------------------------------------|-------------------------------------|
| `size`   | Size of the QR code in pixels                        | 300                                 |
| `margin` | Margin around the QR code in pixels                  | 10                                  |
| `format` | Output format ('png', 'svg', 'eps')                  | 'png' if GD or Imagick is available, otherwise 'svg' |

## Workflow

The following diagram shows the QR code generation workflow:

```mermaid
graph TD
    A[Application Request] --> B[Call QrcodeService]
    B --> C{Choose Output Format}
    C -->|PNG| D[Check GD/Imagick availability]
    D -->|Available| E[Use corresponding backend]
    D -->|Not Available| F[Use SVG backend]
    C -->|SVG| F
    C -->|EPS| G[Use EPS backend]
    E --> H[Return QR code image]
    F --> H
    G --> H
```

## Testing

Run the tests using PHPUnit:

```bash
# Run all tests
./vendor/bin/phpunit packages/bacon-qr-code-bundle/tests

# Run with code coverage
./vendor/bin/phpunit packages/bacon-qr-code-bundle/tests --coverage-html coverage
```

## Contributing

Please see [CONTRIBUTING.md](https://github.com/tourze/php-monorepo/blob/main/CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
