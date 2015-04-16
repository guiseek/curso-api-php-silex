<?php

namespace Api\Beer\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Api\Beer\BeerService;

class BeerServiceProvider implements ServiceProviderInterface
{
    const BEER_SERVICE	= 'beer.service';

    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }
    public function register(Application $app)
    {
        $app[self::BEER_SERVICE] = $app->protect(function () {
            return $this->getService();
        });
    }
    public function boot(Application $app)
    {
    }
    public function getService()
    {
        $service = new BeerService($this->em);
        return $service;
    }
}