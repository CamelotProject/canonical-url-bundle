<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests;

use Camelot\CanonicalUrlBundle\CamelotCanonicalUrlBundle;
use Camelot\CanonicalUrlBundle\DependencyInjection\CamelotCanonicalUrlExtension;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @covers \Camelot\CanonicalUrlBundle\DependencyInjection\CamelotCanonicalUrlExtension
 */
final class CamelotCanonicalUrlBundleTest extends TestCase
{
    public function testGetContainerExtension(): void
    {
        $bundle = new CamelotCanonicalUrlBundle();

        static::assertInstanceOf(CamelotCanonicalUrlExtension::class, $bundle->getContainerExtension());
    }
}
