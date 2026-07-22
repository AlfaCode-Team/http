<?php

declare(strict_types=1);

namespace AlfacodeTeam\PhpServicePlatform\Kernel\Http\Tests;

use AlfacodeTeam\PhpServicePlatform\Kernel\Http\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;

final class UriTest extends TestCase
{
    public function testParsesComponents(): void
    {
        $uri = new Uri('https://user:pass@example.com:8443/api/invoices?page=2#top');

        self::assertSame('https', $uri->getScheme());
        self::assertSame('example.com', $uri->getHost());
        self::assertSame(8443, $uri->getPort());
        self::assertSame('/api/invoices', $uri->getPath());
        self::assertSame('page=2', $uri->getQuery());
        self::assertSame('top', $uri->getFragment());
        self::assertSame('user:pass', $uri->getUserInfo());
    }

    public function testIsImmutable(): void
    {
        $uri = new Uri('https://example.com/a');
        $changed = $uri->withPath('/b')->withQuery('x=1');

        self::assertInstanceOf(UriInterface::class, $changed);
        self::assertSame('/a', $uri->getPath(), 'original must be untouched');
        self::assertSame('/b', $changed->getPath());
        self::assertSame('x=1', $changed->getQuery());
    }

    public function testStringifies(): void
    {
        $uri = (new Uri('https://example.com'))->withPath('/login');

        self::assertStringContainsString('https://example.com', (string) $uri);
        self::assertStringContainsString('/login', (string) $uri);
    }
}
