<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\Tests\Fixtures;

use Camelot\CanonicalUrlBundle\CamelotCanonicalUrlBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new CamelotCanonicalUrlBundle();
    }

    private function getConfigDir(): string
    {
        return dirname(__DIR__, 2) . '/config';
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('framework', [
            'test' => true,
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
            'router' => [
                'default_uri' => 'https://example.org',
            ],
        ]);

//        $configDir = $this->getConfigDir();
//        $container->import($configDir . '/framework.php');
//        $container->import($configDir . '/doctrine.php');
//        $container->import($configDir . '/bundle.php');
//
//        $container->import($configDir . '/services.php');
    }

    private function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__ . '/routes.php');
    }
}

