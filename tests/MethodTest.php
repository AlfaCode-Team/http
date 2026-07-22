<?php

declare(strict_types=1);

namespace AlfacodeTeam\PhpServicePlatform\Kernel\Http\Tests;

use AlfacodeTeam\PhpServicePlatform\Kernel\Http\Method;
use PHPUnit\Framework\TestCase;

final class MethodTest extends TestCase
{
    public function testSafeMethods(): void
    {
        self::assertTrue(Method::GET->isSafe());
        self::assertTrue(Method::HEAD->isSafe());
        self::assertFalse(Method::POST->isSafe());
        self::assertFalse(Method::DELETE->isSafe());
    }

    public function testIdempotentMethods(): void
    {
        self::assertTrue(Method::PUT->isIdempotent());
        self::assertTrue(Method::DELETE->isIdempotent());
        self::assertFalse(Method::POST->isIdempotent());
    }

    public function testFromStringValue(): void
    {
        self::assertSame(Method::POST, Method::from('POST'));
    }

    public function testCacheableMethods(): void
    {
        self::assertTrue(Method::GET->isCacheable());
        self::assertFalse(Method::POST->isCacheable());
    }

    public function testAllReturnsEveryMethodValue(): void
    {
        $all = Method::all();
        self::assertContains('GET', $all);
        self::assertContains('DELETE', $all);
        self::assertCount(count(Method::cases()), $all);
    }
}
