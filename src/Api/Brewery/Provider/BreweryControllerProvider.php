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
	private static $SERIALIZE_TO = 'json';

	public function setBaseRoute($baseRoute)
	{
		$this->baseRoute = $baseRoute;
		return $this;
	}

	public function connect(Application $app)
	{
		$controllers = $app['controllers_factory'];
		$controller = new BreweryController();

		$controllers->get(self::ROUTE.'/{id}/{param}', function ($id, $param) use ($controller, $app) {
			$response = $controller->get($app, $id, $param);

			return new Response(
				$app['serializer']->serialize($response['data'], self::$SERIALIZE_TO),
				$response['code'],
				self::$CONTENT_TYPE
			);
		})->convert('id', function ($id) {
			return (int) $id;
		})->value('id', null)->value('param', null);

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
				return $app->json(['message' => 'Qual cervejaria quer alterar?'], 400);
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
				return $app->json(['message' => 'Qual cervejaria quer apagar?'], 400);
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

		$controllers->match(self::ROUTE.'/{id}/{param}', function ($id, $param, Request $request) use ($app) {
			return new Response('', 200);
		})->method('OPTIONS')->value('id', null)->value('param', null);

		return $controllers;
	}
}