<?php

namespace Api\Beer\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Api\Beer\BeerEntity;
use Api\Brewery\Provider\BreweryServiceProvider;
use Symfony\Component\HttpKernel\KernelEvents;

class BeerControllerProvider implements ControllerProviderInterface
{
	private $baseRoute;
	const ROUTE = '/beer';
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
    	
    	$controllers->get(self::ROUTE.'/{id}', function ($id) use ($app) {
    		$code = null;

    		$repo = $app[BeerServiceProvider::BEER_SERVICE]();
    		if (!$id) {
    			$data = $repo()->findAll();
    		} else {
    			$data = $repo()->find($id);
    			if (!$data) {
    				$code = 404;
    				$data = ['message' => 'Cerveja não encontrada'];
    			}
    		}
   			$response = $app['serializer']->serialize($data,'json');
    		$code = ($code) ? $code : 200;
   			return new Response($response, $code, self::$CONTENT_TYPE);
    	})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);
		
		$controllers->post(self::ROUTE, function (Request $request) use ($app) {
			$code = null;
			$data = $request->request->all();
			$repo_brewery = $app[BreweryServiceProvider::BREWERY_SERVICE]();
			$brewery = $repo_brewery()->find($data['brewery']['id']);
			$beer = new BeerEntity();
			$beer->setFromArray($data);
			$beer->addBrewery($brewery);
			$repo = $app[BeerServiceProvider::BEER_SERVICE]();
			try {
				$data = $repo->create($beer);
			} catch (Exception $e) {
				$code = 500;
				$data = $e->getMessage();
			}
			$code = ($code) ? $code : 201;
			$response = $app['serializer']->serialize($data,'json');
   			return new Response($response, $code, self::$CONTENT_TYPE);
		});
		
		$controllers->put(self::ROUTE.'/{id}', function ($id, Request $request) use ($app) {
			$data = $request->request->all();
			unset($data['created']);

			$repo = $app[BeerServiceProvider::BEER_SERVICE]();
			$beer = $repo()->find($id);
			if (!$beer) {
				return $app->json(['message' => 'Cerveja não encontrada'], 404);
			}

			$repo_brewery = $app[BreweryServiceProvider::BREWERY_SERVICE]();
			$brewery = $repo_brewery()->find($data['brewery']['id']);
			$beer = new BeerEntity();
			$beer->setFromArray($data);
			$beer->addBrewery($brewery);
			$data = $repo->update($beer);
			$response = $app['serializer']->serialize($data,'json');
   			return new Response($response, 200, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->delete(self::ROUTE.'/{id}', function ($id) use ($app) {
			$repo = $app[BeerServiceProvider::BEER_SERVICE]();
			$beer = $repo()->find($id);
			if (!$beer) {
				return $app->json(['message' => 'Cerveja não encontrada'], 404);
			}
			$data = $repo->delete($beer);
			$response = $app['serializer']->serialize($data,'json');
   			return new Response($response, 200, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);
		
		$controllers->match(self::ROUTE.'/{id}', function ($id, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null);
		
		return $controllers;
	}
}