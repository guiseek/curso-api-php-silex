<?php

namespace Api\Brewery;

use Silex\Application;
use Api\Brewery\Provider\BreweryServiceProvider;
use Api\Beer\Provider\BeerServiceProvider;

class BreweryController
{
    public function get($app, $id, $param)
    {
        $code = null;

        $breweryService = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        if (!$id) {
            $data = $breweryService()->findAll();
        } else {
            $data = $breweryService()->find($id);
            if (!$data) {
                $code = 404;
                $data = ['message' => 'Cervejaria não encontrada'];
            }
            if ($param) {
                $beerService = $app[BeerServiceProvider::BEER_SERVICE]();
                $data = $beerService()->findBy(['brewery' => $data]);
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

        $brewery = new BreweryEntity();
        $brewery->setFromArray($data);

        $response = [];
        $errors = $app['validator']->validate($brewery);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $response[] = ['message' => $error->getMessage()];
            }
            return ['data' => $response, 'code' => 422];
        }

        $breweryService = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        try {
            $data = $breweryService->create($brewery);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return ['data' => $data, 'code' => ($code) ? $code : 201];
    }

    public function put($app, $id, $data)
    {
        $code = null;

        $breweryService = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = $breweryService()->find($id);
        if (!$brewery) {
            return ['data' => ['message' => 'Essa cervejaria existe?'], 'code' => 404];
        }

        unset($data['created']);

        $brewery = new BreweryEntity();
        $brewery->setFromArray($data);

        $response = [];
        $errors = $app['validator']->validate($brewery);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $response[] = ['message' => $error->getMessage()];
            }
            return ['data' => $response, 'code' => 422];
        }

        try {
            $data = $breweryService->update($brewery);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return ['data' => $data, 'code' => ($code) ? $code : 200];
    }

    public function delete($app, $id)
    {
        $code = null;

        $breweryService = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = $breweryService()->find($id);

        if (!$brewery) {
            return ['data' => ['message' => 'Essa cervejaria existe?'], 'code' => 404];
        }

        try {
            $data = $breweryService->delete($brewery);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return ['data' => $data, 'code' => ($code) ? $code : 200];
    }
}