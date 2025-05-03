# Bacon QR Code Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/bacon-qr-code-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/bacon-qr-code-bundle)
[![License](https://img.shields.io/github/license/tourze/php-monorepo.svg?style=flat-square)](https://github.com/tourze/php-monorepo/blob/main/LICENSE)

A Symfony bundle for [bacon/bacon-qr-code](https://github.com/Bacon/BaconQrCode) library that provides QR code generation capabilities in your Symfony application.

## Features

- Easy integration with Symfony applications
- Multiple output formats support (PNG, SVG, EPS)
- Customizable QR code size and margin
- Simple URL generation for QR codes
- Automatic detection of available image libraries (GD, Imagick)

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
<img src="{{ qrCodeUrl }}" alt="QR Code">

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

## Available Options

When generating QR codes, you can customize the following options:

| Option   | Description                                          | Default Value                       |
|----------|------------------------------------------------------|-------------------------------------|
| `size`   | Size of the QR code in pixels                        | 300                                 |
| `margin` | Margin around the QR code in pixels                  | 10                                  |
| `format` | Output format ('png', 'svg', 'eps')                  | 'png' if GD or Imagick is available, otherwise 'svg' |

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
