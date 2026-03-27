<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class RoleMiddleware implements MiddlewareInterface
{
    public function __construct(private array $rolesRequis) {}

    public function process(Request $request, Handler $handler): Response
    {
        $role = $_SESSION['user_role'] ?? null;
        if ($role !== 'admin' && !in_array($role, $this->rolesRequis)) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/connexion')->withStatus(302);
        }

        return $handler->handle($request);
    }
}