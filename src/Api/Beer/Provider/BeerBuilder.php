<?php

namespace Api\Beer\Provider;

use Silex\Application;

class BeerBuilder
{
    public static function mountProviderIntoApplication($route, Application $app)
    {
        $app->register(new BeerServiceProvider($app['em']));
        $app->mount($route, (new BeerControllerProvider())->setBaseRoute($route));
    }
}