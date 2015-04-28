<?php

namespace Api;

use Silex\Application as SilexApplication;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerBuilder;
use Api\Brewery\Provider\BreweryBuilder;
use Api\Beer\Provider\BeerBuilder;

class Application extends SilexApplication
{
    private $baseRouteApi	= '/api/v1';
    private $mediaTypes = ['application/json'];

    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->register(new ValidatorServiceProvider());
        $this['serializer'] = SerializerBuilder::create()->build();

        BreweryBuilder::mountProviderIntoApplication($this->baseRouteApi, $this);
        BeerBuilder::mountProviderIntoApplication($this->baseRouteApi, $this);

        $this->before(function (Request $request) {
            if ($request->getMethod() == 'OPTIONS') {
                return;
            }
            $tokens = require_once __DIR__ . '/../../config/token.php';
            if (!$tokens) {
                throw new \Exception("Error Processing Token file", 1);
            }
            if(!$request->headers->has('X-Token')) {
                return $this->json(['message' => 'Unauthorized'], 401);
            }
            if (!in_array($request->headers->get('X-Token'), array_keys($tokens))) {
                return $this->json(['message' => 'Forbidden'], 403);
            }
            if (!in_array($request->headers->get('Content-Type'), $this->mediaTypes)) {
                return $this->json(['message' => 'Not Acceptable'], 406);
            }
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        });

        $this->error(function (\Exception $e, $code) {
            switch ($code) {
                case 404:
                    $message = ['message' => 'A URL requisitada não foi encontrada.'];
                    break;
                default:
                    $message = ['message' => 'Ops, algo deu errado.'];
            }
            return $this->json($message, $code);
        });

        $this->after(function (Request $request, Response $response) {
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, X-Token');
        });

    }
}