<?php

namespace Api\Beer;

use Silex\Application;
use Api\Beer\Provider\BeerServiceProvider;
use Api\Brewery\Provider\BreweryServiceProvider;

class BeerController
{
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
        return [
            'data' => $data,
            'code' => ($code) ? $code : 200
        ];
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
        return [
            'data' => $data,
            'code' => ($code) ? $code : 201
        ];
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
        return [
            'data' => $data,
            'code' => 200
        ];
    }

    public function delete($app, $id)
    {
        $code = null;

        $repo = $app[BeerServiceProvider::BEER_SERVICE]();
        $beer = $repo()->find($id);
        if (!$beer) {
            return $app->json(['message' => 'Cerveja não encontrada'], 404);
        }
        $data = $repo->delete($beer);
        return [
            'data' => $data,
            'code' => 200
        ];
    }
}