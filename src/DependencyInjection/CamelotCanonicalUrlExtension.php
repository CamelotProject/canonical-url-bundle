<?php

declare(strict_types=1);

namespace Camelot\CanonicalUrlBundle\DependencyInjection;

use Camelot\CanonicalUrlBundle\EventListener\ExceptionListener;
use Camelot\CanonicalUrlBundle\EventListener\RequestListener;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;
use function dirname;

final class CamelotCanonicalUrlExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('camelot_canonical_url.config', $config);

        $container->getDefinition(RequestListener::class)
            ->setArgument('$redirect', $config['redirect'])
            ->setArgument('$redirectCode', $config['redirect_code'])
        ;

        $container->getDefinition(ExceptionListener::class)
            ->setArgument('$redirect', $config['redirect'])
            ->setArgument('$redirectCode', $config['redirect_code'])
            ->setArgument('$trailingSlash', $config['trailing_slash'])
        ;
    }
}
