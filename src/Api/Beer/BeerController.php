<?php

namespace Api\Beer;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Api\Beer\Provider\BeerServiceProvider;
use Api\Brewery\Provider\BreweryServiceProvider;

class BeerController
{
    private static $CONTENT_TYPE = ['Content-Type' => 'application/json'];

    public function get($app, $id)
    {
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
    }

    public function post($app, $request)
    {
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
    }

    public function put($app, $id, $request)
    {
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
    }

    public function delete($app, $id)
    {
        $repo = $app[BeerServiceProvider::BEER_SERVICE]();
        $beer = $repo()->find($id);
        if (!$beer) {
            return $app->json(['message' => 'Cerveja não encontrada'], 404);
        }
        $data = $repo->delete($beer);
        $response = $app['serializer']->serialize($data,'json');
        return new Response($response, 200, self::$CONTENT_TYPE);
    }
}