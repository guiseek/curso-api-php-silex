<?php

namespace Api\Brewery\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Api\Brewery\BreweryService;

class BreweryServiceProvider implements ServiceProviderInterface
{
    const BREWERY_SERVICE	= 'brewery.service';

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
    public function register(Application $app)
    {
        $app[self::BREWERY_SERVICE] = $app->protect(function () {
            return $this->getService();
        });
    }
    public function boot(Application $app)
    {
    }
    public function getService()
    {
        $service = new BreweryService($this->em);
        return $service;
    }
}