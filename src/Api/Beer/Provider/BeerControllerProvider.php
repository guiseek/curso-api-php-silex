<?php

namespace Api\Beer\Provider;

use Api\Beer\BeerController;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
		$controller = new BeerController();

		$controllers->get(self::ROUTE.'/{id}', function ($id) use ($controller, $app) {
			$response = $controller->get($app, $id);
			$data = $app['serializer']->serialize($response['data'],'json');
			$code = $response['code'];
			return new Response($data, $code, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->post(self::ROUTE, function (Request $request) use ($controller, $app) {
			$response = $controller->post($app, $request);
			$data = $app['serializer']->serialize($response['data'],'json');
			$code = $response['code'];
			return new Response($data, $code, self::$CONTENT_TYPE);
		});

		$controllers->put(self::ROUTE.'/{id}', function ($id, Request $request) use ($controller, $app) {
			$response = $controller->put($app, $id, $request);
			$data = $app['serializer']->serialize($response['data']);
			$code = $response['code'];
			return new Response($data, $code, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->delete(self::ROUTE.'/{id}', function ($id) use ($controller, $app) {
			$response = $controller->delete($app, $id);
			$data = $response['data'];
			$code = $response['code'];
			return new Response($data, $code, self::$CONTENT_TYPE);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->match(self::ROUTE.'/{id}', function ($id, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null);

		return $controllers;
	}
}