<?php

namespace Api\Brewery\Provider;

use Silex\Application;

class BreweryBuilder
{
    public static function mountProviderIntoApplication($route, Application $app)
    {
        $app->register(new BreweryServiceProvider($app['em']));
        $app->mount($route, (new BreweryControllerProvider())->setBaseRoute($route));
    }
}