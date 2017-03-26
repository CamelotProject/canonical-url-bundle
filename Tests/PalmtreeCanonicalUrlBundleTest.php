<?php

namespace Palmtree\CanonicalUrlBundle\Tests;

use Palmtree\CanonicalUrlBundle\DependencyInjection\Compiler\CompilerPass;
use Palmtree\CanonicalUrlBundle\DependencyInjection\PalmtreeCanonicalUrlExtension;
use Palmtree\CanonicalUrlBundle\PalmtreeCanonicalUrlBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PalmtreeCanonicalUrlBundleTest extends TestCase
{
    public function testGetContainerExtension()
    {
        $bundle = new PalmtreeCanonicalUrlBundle();

        $this->assertInstanceOf(
            PalmtreeCanonicalUrlExtension::class,
            $bundle->getContainerExtension()
        );
    }

    public function testCompilerPass()
    {
        $bundle = new PalmtreeCanonicalUrlBundle();

        $container = new ContainerBuilder();

        $bundle->build($container);

        $passes = $container->getCompilerPassConfig()->getPasses();
        $found  = false;

        foreach ($passes as $pass) {
            if ($pass instanceof CompilerPass) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
    }
}
