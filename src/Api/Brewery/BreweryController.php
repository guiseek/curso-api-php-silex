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

        $repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        if (!$id) {
            $data = $repo()->findAll();
        } else {
            $data = $repo()->find($id);
            if (!$data) {
                $code = 404;
                $data = ['message' => 'Cervejaria não encontrada'];
            }
            if ($param) {
                $repo_beer = $app[BeerServiceProvider::BEER_SERVICE]();
                $data = $repo_beer()->findBy(['brewery' => $data]);
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
        $repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = new BreweryEntity();
        $brewery->setFromArray($data);
        $brewery = $repo->create($brewery);

        try {
            $data = $repo->create($brewery);
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
        $code = null;

        $data = $request->request->all();
        unset($data['created']);

        $repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = $repo()->find($id);
        if (!$brewery) {
            return $app->json(['message' => 'Cervejaria não encontrada'], 404);
        }

        $brewery = new BreweryEntity();
        $brewery->setFromArray($data);

        try {
            $data = $repo->update($brewery);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return [
            'data' => $data,
            'code' => $code
        ];
    }

    public function delete($app, $id)
    {
        $code = null;

        $repo = $app[BreweryServiceProvider::BREWERY_SERVICE]();
        $brewery = $repo()->find($id);
        if (!$brewery) {
            return $app->json(['message' => 'Cervejaria não encontrada'], 404);
        }

        try {
            $data = $repo->delete($brewery);
        } catch (Exception $e) {
            $code = 500;
            $data = $e->getMessage();
        }
        return [
            'data' => $data,
            'code' => $code
        ];
    }
}