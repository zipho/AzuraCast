<?php
namespace App;

use App\Http\Factory\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App as SlimApp;

class App extends SlimApp
{
    /**
     * @inheritDoc
     */
    public function run(?ServerRequestInterface $request = null): void
    {
        if (null === $request) {
            $request = $this->createServerRequest();
        }

        parent::run($request);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $request = $this->decorateServerRequest($request);

        return parent::handle($request);
    }

    /**
     * @return ServerRequestInterface
     */
    public function createServerRequest(): ServerRequestInterface
    {
        $requestCreator = new ServerRequestFactory;
        return $requestCreator->createServerRequestFromGlobals();
    }

    public function decorateServerRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        $requestCreator = new ServerRequestFactory;
        return $requestCreator->decorateServerRequest($request);
    }
}