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

        $beerService = $app[BeerServiceProvider::BEER_SERVICE]();
        if (!$id) {
            $data = $beerService()->findAll();
        } else {
            $data = $beerService()->find($id);
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

    public function post($app, $data)
    {
        $code = null;

        $breweryService = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = $breweryService()->find($data['brewery']['id']);

        $beer = new BeerEntity();
        $beer->setFromArray($data);
        $beer->addBrewery($brewery);

        $response = [];
        $errors = $app['validator']->validate($beer);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $response[] = ['message' => $error->getMessage()];
            }
            return ['data' => $response, 'code' => 422];
        }

        $beerService = $app[BeerServiceProvider::BEER_SERVICE]();
        try {
            $data = $beerService->create($beer);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return ['data' => $data, 'code' => ($code) ? $code : 201];
    }

    public function put($app, $id, $data)
    {
        $code = null;

        $beerService = $app[BeerServiceProvider::BEER_SERVICE]();
        $beer = $beerService()->find($id);
        if (!$beer) {
            return ['data' => ['message' => 'Essa cerveja existe?'], 'code' => 404];
        }

        unset($data['created']);

        $breweryService = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = $breweryService()->find($data['brewery']['id']);

        $beer = new BeerEntity();
        $beer->setFromArray($data);
        $beer->addBrewery($brewery);

        $response = [];
        $errors = $app['validator']->validate($beer);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $response[] = ['message' => $error->getMessage()];
            }
            return ['data' => $response, 'code' => 422];
        }

        try {
            $data = $beerService->update($beer);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return ['data' => $data, 'code' => ($code) ? $code : 200];
    }

    public function delete($app, $id)
    {
        $code = null;

        $beerService = $app[BeerServiceProvider::BEER_SERVICE]();
        $beer = $beerService()->find($id);

        if (!$beer) {
            return ['data' => ['message' => 'Essa cerveja existe?'], 'code' => 404];
        }

        try {
            $data = $beerService->delete($beer);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return ['data' => $data, 'code' => ($code) ? $code : 200];
    }
}