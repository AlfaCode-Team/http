# Changelog

All notable changes to `alfacode-team/http` are documented here. The format is
based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and this project
adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- Initial extraction of the PhpServicePlatform kernel HTTP layer into a standalone,
  MIT-licensed package.
- Immutable `Request` and single-type `Response` value objects.
- PSR-7 `Uri`, host-aware `SiteUri`, Accept-* `Negotiate`, HTTP `Method` enum,
  `UserAgent` parser, FPM-/Swoole-safe `UploadedFile`.
- `Contracts\RequestAware` seam and `Concerns\ManagesResponse` shared accessors.
