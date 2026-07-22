# AlfaCode HTTP

[![CI](https://github.com/AlfaCode-Team/http/actions/workflows/ci.yml/badge.svg)](https://github.com/AlfaCode-Team/http/actions/workflows/ci.yml)
[![License: MIT](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![PHP ^8.2](https://img.shields.io/badge/php-%5E8.2-777bb4.svg)](https://www.php.net/)

The HTTP layer of the **PhpServicePlatform** kernel: a small, immutable
`Request`/`Response` surface with a PSR-7 `Uri`, content negotiation, absolute-URL
generation, and FPM- **and** OpenSwoole-safe file uploads. Built on
[Symfony HttpFoundation](https://symfony.com/doc/current/components/http_foundation.html)
for a battle-tested parser/emitter, but exposed through its own API so callers never
depend on Symfony types directly.

> Package: `alfacode-team/http` ·
> Namespace: `AlfacodeTeam\PhpServicePlatform\Kernel\Http\`

## Why

- **Immutable `Request`.** Every mutator (`withHeader`, `withAttribute`, `merge`, …)
  returns a *new* instance; `__clone()` deep-clones parameter bags so clones are fully
  isolated — required for OpenSwoole/coroutine safety.
- **One `Response` type.** Named constructors (`json`, `html`, `redirect`, `stream`,
  `download`, …) instead of a zoo of response classes.
- **No hidden globals.** Nothing in this layer reaches for a container or config
  singleton; dependencies are passed in.
- **Engine-agnostic API.** Consumers use `$request->input()` / `Response::json()`,
  never `Symfony\...\Request` — so the underlying engine can change without breaking you.

## Install

```bash
composer require alfacode-team/http
```

Requires PHP 8.2+ and the `symfony/http-foundation` + `symfony/mime` runtime deps
(installed automatically).

## Usage

### Reading the request (immutable)

```php
use AlfacodeTeam\PhpServicePlatform\Kernel\Http\Request;

$request = Request::capture();

$request->method();                 // 'POST' (always upper-case)
$request->path();                   // '/api/invoices'
$request->isMethod('post');         // true
$request->isSecure();               // honours X-Forwarded-Proto

// Input — body + query merged; JSON bodies decoded automatically
$request->input('title', 'Untitled');
$request->all();
$request->only(['title', 'amount']);
$request->boolean('active');        // "1"/"true"/"on"/"yes" → true
$request->integer('page');
$request->query('page');
$request->header('Accept');         // case-insensitive
$request->bearerToken();            // from Authorization: Bearer …
$file = $request->file('avatar');   // ?UploadedFile

// Routing / negotiation helpers
$request->segments();               // ['api', 'invoices']
$request->is('api/*');              // wildcard path match
$request->expectsJson();
```

Immutable mutators return a **new** request, leaving the original untouched:

```php
$request = $request->withAttribute('locale', 'fr')
                   ->withHeader('X-Trace', $id)
                   ->merge(['source' => 'import']);
```

### Building a response (one type, immutable)

```php
use AlfacodeTeam\PhpServicePlatform\Kernel\Http\Response;

return Response::json($data, 201);
return Response::created($data, location: "/api/invoices/{$id}");
return Response::noContent();                        // 204
return Response::redirect('/login');

// Error envelopes: { "error": { "code", "message"[, "fields"] } }
return Response::notFound();
return Response::unprocessable(['email' => 'Required.']);   // 422
return Response::tooManyRequests(retryAfter: 30);

// Streaming / files — work on BOTH PHP-FPM and OpenSwoole
return Response::stream(fn () => print(generateCsv()));
return Response::download($path, 'report.pdf');

// Immutable chaining
return Response::json($data)
    ->withHeader('Cache-Control', 'no-store')
    ->withCookie('sid', $token, maxAge: 3600);
```

### URLs & negotiation

```php
// PSR-7 Uri from the current request
$login = (string) $request->uri()->withPath('/login')->withQuery('');

// Host-aware absolute URLs (OAuth callbacks, email links, sitemaps)
$callback = $request->site()->to('auth/callback');

// Content negotiation from Accept-* headers
$locale = $request->negotiate()->language(['en', 'fr', 'ar']);
$type   = $request->negotiate()->media(['application/json', 'text/csv']);
```

### Typed HTTP methods

```php
use AlfacodeTeam\PhpServicePlatform\Kernel\Http\Method;

$m = Method::from($request->method());
$m->isSafe();        // GET/HEAD/OPTIONS/TRACE
$m->isIdempotent();  // + PUT/DELETE
```

### Uploads — FPM-safe and Swoole-safe

```php
$file = $request->file('avatar');
if ($file !== null && $file->isValid()) {
    $file->move($dir, $generatedName);   // move_uploaded_file on FPM; rename on Swoole
}
```

## What's inside

| Class | Role |
| --- | --- |
| `Request` | Final, immutable request; extends Symfony's `Request` behind the kernel API |
| `Response` | Single response type — json/html/text/stream/download/redirect/… |
| `UploadedFile` | FPM-safe (`is_uploaded_file` check) and Swoole-safe (`fromSwoole`) |
| `Uri` | Immutable PSR-7 `UriInterface` → `Request::uri()` |
| `SiteUri` | Absolute-URL generator (host-aware) → `Request::site()` |
| `Negotiate` | Accept-* content negotiation → `Request::negotiate()` |
| `Method` | HTTP method enum with `isSafe()` / `isIdempotent()` semantics |
| `UserAgent` | Parsed user-agent value object |
| `Concerns\ManagesResponse` | Shared immutable response accessors/mutators |
| `Contracts\RequestAware` | `setRequest(Request): static` — the only kernel↔controller seam |

## Notes for standalone use

This package is extracted from the PhpServicePlatform kernel. A couple of methods —
`Request::withIdentity()`/`identity()` and `Request::withContainer()`/`container()` —
type-hint the kernel's `Identity` and `ModuleContainer`. Those are **optional**: they
are only resolved when a host framework calls them, so the request/response/URI/upload
surface is fully usable on its own.

## Testing

```bash
composer install
composer test        # phpunit
composer analyse     # phpstan
composer check       # both
```

## Contributing

Contributions are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md). Please run
`composer check` before opening a pull request. This project follows a
[Code of Conduct](CODE_OF_CONDUCT.md); by participating you agree to uphold it.

## Security

Found a vulnerability? Please follow the [Security Policy](SECURITY.md) and report
it privately — never in a public issue.

## License

MIT © AlfaCode Team — see [LICENSE](LICENSE).
