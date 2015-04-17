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
	private static $SERIALIZE_TO = 'json';

	public function setBaseRoute($baseRoute)
	{
		$this->baseRoute = $baseRoute;
		return $this;
	}

	public function connect(Application $app)
	{
		$controllers = $app['controllers_factory'];
		$controller = new BeerController();

		$controllers->get(self::ROUTE.'/{id}', function ($id) use ($controller, $app) {
			$response = $controller->get($app, $id);

			return new Response(
				$app['serializer']->serialize($response['data'], self::$SERIALIZE_TO),
				$response['code'],
				self::$CONTENT_TYPE
			);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->post(self::ROUTE, function (Request $request) use ($controller, $app) {
			$data = $request->request->all();
			$response = $controller->post($app, $data);

			return new Response(
				$app['serializer']->serialize($response['data'], self::$SERIALIZE_TO),
				$response['code'],
				self::$CONTENT_TYPE
			);
		});

		$controllers->put(self::ROUTE.'/{id}', function ($id, Request $request) use ($controller, $app) {
			if (!$request->get('id')) {
				return $app->json(['message' => 'Qual cerveja quer alterar?'], 400);
			}

			$data = $request->request->all();
			$response = $controller->put($app, $id, $data);

			return new Response(
				$app['serializer']->serialize($response['data'], self::$SERIALIZE_TO),
				$response['code'],
				self::$CONTENT_TYPE
			);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->delete(self::ROUTE.'/{id}', function ($id, Request $request) use ($controller, $app) {
			if (!$request->get('id')) {
				return $app->json(['message' => 'Qual cerveja quer apagar?'], 400);
			}

			$response = $controller->delete($app, $id);

			return new Response(
				$app['serializer']->serialize($response['data'], self::$SERIALIZE_TO),
				$response['code'],
				self::$CONTENT_TYPE
			);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null);

		$controllers->match(self::ROUTE.'/{id}', function ($id, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null);

		return $controllers;
	}
}