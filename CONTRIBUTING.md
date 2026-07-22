# Contributing

Thanks for your interest in improving `alfacode-team/http`.

## Getting started

```bash
git clone git@github.com:AlfaCode-Team/http.git
cd http
composer install
```

## Before you open a pull request

- Run the full check: `composer check` (PHPStan + PHPUnit).
- Add or update tests for any behaviour you change.
- Keep the public API immutable — mutators must return a **new** instance, never
  mutate `$this`. This is required for OpenSwoole/coroutine safety.
- Do not introduce hidden globals (no container/config singletons in this layer);
  dependencies are passed in.
- Consumers must be able to depend on this package's own method surface, never on
  Symfony types directly.

## Coding standards

- PHP 8.2+, `declare(strict_types=1);` in every file.
- Follow the existing formatting (PSR-12-ish, aligned to the surrounding code).

## Reporting issues

Please use the [issue tracker](https://github.com/AlfaCode-Team/http/issues) and
include the PHP version, a minimal reproduction, and the expected vs. actual result.

## License

By contributing, you agree that your contributions will be licensed under the MIT
License.
