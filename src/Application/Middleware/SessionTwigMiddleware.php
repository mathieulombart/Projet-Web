<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Views\Twig;

class SessionTwigMiddleware implements MiddlewareInterface
{
    public function __construct(private Twig $twig) {}

    public function process(Request $request, Handler $handler): Response
    {
        $this->twig->getEnvironment()->addGlobal('user_id',   $_SESSION['user_id']   ?? null);
        $this->twig->getEnvironment()->addGlobal('user_role', $_SESSION['user_role'] ?? null);

        return $handler->handle($request);
    }
}