<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->add('foo', '/foo');
    $routes->add('foo/{noop}', '/foo/{noop}');
    $routes->add('baz', '/baz/');
};
