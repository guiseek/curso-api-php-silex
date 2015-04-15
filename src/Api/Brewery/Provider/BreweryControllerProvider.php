<?php

namespace Api\Brewery\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Api\Brewery\BreweryEntity;
use Api\Beer\Provider\BeerServiceProvider;

class BreweryControllerProvider implements ControllerProviderInterface
{
	private $baseRoute;
	const ROUTE = '/brewery';
	private static $CONTENT_TYPE = ['Content-Type' => 'application/json'];
	
	public function setBaseRoute($baseRoute)
	{
		$this->baseRoute = $baseRoute;
		return $this;
	}
		
    public function connect(Application $app)
    {
    	return $this->extractControllers($app);
    }
    
    private function extractControllers(Application $app)
    {
    	$controllers = $app['controllers_factory'];
    	
    	$controllers->get(self::ROUTE.'/{id}/{param}', function ($id, $param) use ($app) {
    		$code = null;
    		$repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
    		if (!$id) {
    			$data = $repo()->findAll();
    		} else {
    			$data = $repo()->find($id);
    			if (!$data) {
    				$code = 404;
    				$data = ['message' => 'Cervejaria não encontrada'];
    			} else {
					if ($param) {
						$repo_beer = $app[BeerServiceProvider::BEER_SERVICE]();
						$data = $repo_beer()->findBy(['brewery' => $data]);
					}
				}
    		}
    		$response = $app['serializer']->serialize($data,'json');
   			$code = ($code) ? $code : 200;
   			return new Response($response, $code, self::$CONTENT_TYPE);
    	})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null)->value('param', null);

		$controllers->post(self::ROUTE, function (Request $request) use ($app) {
			$data = $request->request->all();
			$repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
			$brewery = new BreweryEntity();
			$brewery->setFromArray($data);
			$brewery = $repo->create($brewery);
			$response = $app['serializer']->serialize($brewery,'json');
   			return new Response($response, 200, self::$CONTENT_TYPE);
		});
		
		$controllers->put(self::ROUTE.'/{id}', function ($id, Request $request) use ($app) {
			$data = $request->request->all();
			unset($data['created']);
			unset($data['beers']);
			$repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
			$brewery = $repo()->find($id);
			if (!$brewery) {
				return $app->json(['message' => 'Cervejaria não encontrada'], 404);
			}
			$brewery->setFromArray($data);
			$brewery = $repo->update($brewery);
			$response = $app['serializer']->serialize($brewery,'json');
			return new Response($response, 200, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->delete(self::ROUTE.'/{id}', function ($id) use ($app) {
			$repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
			$brewery = $repo()->find($id);
			if (!$brewery) {
				return $app->json(['message' => 'Cervejaria não encontrada'], 404);
			} else {
				$brewery = $repo->delete($brewery);
			}
			$response = $app['serializer']->serialize($brewery,'json');
			return new Response($response, 200, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);
		
		$controllers->match(self::ROUTE.'/{id}/{param}', function ($id, $param, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null)->value('param', null);
		
		return $controllers;
	}
}