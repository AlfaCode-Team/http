<?php

declare(strict_types=1);

namespace AlfacodeTeam\PhpServicePlatform\Kernel\Http\Tests;

use AlfacodeTeam\PhpServicePlatform\Kernel\Http\Request;
use PHPUnit\Framework\TestCase;

final class RequestTest extends TestCase
{
    public function testReadsMethodAndPath(): void
    {
        $request = Request::create('/api/invoices?page=2', 'POST');

        self::assertSame('POST', $request->method());
        self::assertSame('/api/invoices', $request->path());
        self::assertTrue($request->isMethod('post'));
    }

    public function testMergesQueryAndBodyInput(): void
    {
        $request = Request::create('/search?page=2', 'POST', ['title' => 'Hello']);

        self::assertSame('Hello', $request->input('title'));
        self::assertSame('2', $request->input('page'));
        self::assertSame('fallback', $request->input('missing', 'fallback'));
    }

    public function testTypedAccessors(): void
    {
        $request = Request::create('/', 'GET', ['active' => 'yes', 'page' => '3']);

        self::assertTrue($request->boolean('active'));
        self::assertSame(3, $request->integer('page'));
    }

    public function testWithAttributeIsImmutable(): void
    {
        $request = Request::create('/', 'GET');
        $next = $request->withAttribute('locale', 'fr');

        self::assertNotSame($request, $next);
        self::assertNull($request->attribute('locale'), 'original must be untouched');
        self::assertSame('fr', $next->attribute('locale'));
    }

    public function testNegotiatesLanguage(): void
    {
        $request = Request::create('/', 'GET');
        $request->headers->set('Accept-Language', 'fr-FR,fr;q=0.9,en;q=0.5');

        self::assertSame('fr', $request->negotiate()->language(['en', 'fr', 'ar']));
    }
}
