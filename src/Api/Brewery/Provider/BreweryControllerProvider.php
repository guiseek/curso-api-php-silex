<?php

namespace Api\Brewery\Provider;

use Api\Brewery\BreweryController;
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
		$controller = new BreweryController();

		$controllers->get(self::ROUTE.'/{id}/{param}', function ($id, $param) use ($controller, $app) {
			$data = $controller->get($app, $id, $param);
			$response = $app['serializer']->serialize($data['data'], 'json');
			$code = $data['code'];
			return new Response($response, $code, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null)->value('param', null);

		$controllers->post(self::ROUTE, function (Request $request) use ($controller, $app) {
			$data = $controller->post($app, $request);
			$response = $app['serializer']->serialize($data['data'],'json');
			$code = $data['code'];
			return new Response($response, $code, self::$CONTENT_TYPE);
		});

		$controllers->put(self::ROUTE.'/{id}', function ($id, Request $request) use ($controller, $app) {
			$data = $controller->put($app, $id, $request);
			$response = $app['serializer']->serialize($data['response'],'json');
			$code = $data['code'];
			return new Response($response, $code, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->delete(self::ROUTE.'/{id}', function ($id) use ($controller, $app) {
			$data = $controller->delete($app, $id);
			$response = $app['serializer']->serialize($data['response'],'json');
			$code = $data['code'];
			return new Response($response, $code, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->match(self::ROUTE.'/{id}/{param}', function ($id, $param, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null)->value('param', null);

		return $controllers;
	}
}