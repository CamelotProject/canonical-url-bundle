<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests;

use Camelot\CanonicalUrlBundle\CamelotCanonicalUrlBundle;
use Camelot\CanonicalUrlBundle\DependencyInjection\CamelotCanonicalUrlExtension;
use Camelot\CanonicalUrlBundle\DependencyInjection\Compiler\CompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CamelotCanonicalUrlBundleTest extends TestCase
{
    public function testGetContainerExtension(): void
    {
        $bundle = new CamelotCanonicalUrlBundle();

        static::assertInstanceOf(
            CamelotCanonicalUrlExtension::class,
            $bundle->getContainerExtension()
        );
    }

    public function testCompilerPass(): void
    {
        $bundle = new CamelotCanonicalUrlBundle();

        $container = new ContainerBuilder();

        $bundle->build($container);

        $passes = $container->getCompilerPassConfig()->getPasses();
        $found = false;

        foreach ($passes as $pass) {
            if ($pass instanceof CompilerPass) {
                $found = true;
                break;
            }
        }

        static::assertTrue($found);
    }
}
