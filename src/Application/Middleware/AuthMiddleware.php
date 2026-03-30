<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {

        if (empty($_SESSION['user_id'])) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/permission')->withStatus(302);
        }

        return $handler->handle($request);
    }
}