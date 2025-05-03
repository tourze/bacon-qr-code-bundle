# QR Code Generation Workflow

This document describes the workflow of QR code generation in the Bacon QR Code Bundle.

## Basic Workflow

```mermaid
graph TD
    A[Application Request] --> B[Call QrcodeService]
    B --> C{Select Output Format}
    C -->|PNG| D[Check if GD/Imagick available]
    D -->|Yes| E[Use corresponding backend for rendering]
    D -->|No| F[Use SVG backend as fallback]
    C -->|SVG| F[Use SVG backend for rendering]
    C -->|EPS| G[Use EPS backend for rendering]
    E --> H[Return QR code image]
    F --> H
    G --> H
```

## Detailed Process

1. **Request Initiation**:
   - Direct call to `QrcodeService::generateQrCode()`
   - URL-based request via `GenerateController::renderCode()`

2. **Parameter Processing**:
   - Data to encode (required parameter)
   - Size (default: 300px)
   - Margin (default: 10px)
   - Format (default: determined by available libraries)

3. **Backend Selection**:
   - If format is PNG:
     - Try GD if available
     - Try Imagick if available
     - Fall back to SVG if neither is available
   - If format is SVG:
     - Use SVG backend
   - If format is EPS:
     - Use EPS backend

4. **Rendering Process**:
   - Create appropriate renderer
   - Initialize Writer with the renderer
   - Generate QR code content
   - Set correct MIME type in response

5. **Response Delivery**:
   - Return HTTP response with proper content type
   - PNG: `image/png`
   - SVG: `image/svg+xml`
   - EPS: `application/postscript`

## Integration Patterns

### Template Integration

```mermaid
sequenceDiagram
    participant C as Controller
    participant S as QrcodeService
    participant T as Template
    participant U as User Browser

    C->>S: getImageUrl(data)
    S-->>C: QR code URL
    C->>T: Render with URL
    T-->>U: HTML with img tag
    U->>S: Request QR code image
    S-->>U: QR code image
```

### Direct Generation

```mermaid
sequenceDiagram
    participant C as Controller
    participant S as QrcodeService
    participant U as User Browser

    C->>S: generateQrCode(data, options)
    S-->>C: HTTP Response with QR code
    C-->>U: Return response directly
```
