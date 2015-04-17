<?php

namespace Api;

use Silex\Application as SilexApplication;
use Silex\Provider\ValidatorServiceProvider;
use JMS\Serializer\SerializerBuilder;
use Api\Brewery\Provider\BreweryBuilder;
use Api\Beer\Provider\BeerBuilder;

class Application extends SilexApplication
{
    private $baseRouteApi	= '/api/v1';

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->register(new ValidatorServiceProvider());
        $this['serializer'] = SerializerBuilder::create()->build();

        BreweryBuilder::mountProviderIntoApplication($this->baseRouteApi, $this);
        BeerBuilder::mountProviderIntoApplication($this->baseRouteApi, $this);
    }
}