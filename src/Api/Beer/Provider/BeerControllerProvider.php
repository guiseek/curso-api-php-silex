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
			return $controller->get($app, $id);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->post(self::ROUTE, function (Request $request) use ($controller, $app) {
			return $controller->post($app, $request);
		});

		$controllers->put(self::ROUTE.'/{id}', function ($id, Request $request) use ($controller, $app) {
			return $controller->put($app, $id, $request);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->delete(self::ROUTE.'/{id}', function ($id) use ($controller, $app) {
			return $controller->delete($app, $id);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->match(self::ROUTE.'/{id}', function ($id, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null);

		return $controllers;
	}
}