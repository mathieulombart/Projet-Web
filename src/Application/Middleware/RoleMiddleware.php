<?php

namespace App\Application\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

class RoleMiddleware implements MiddlewareInterface
{
    private array $rolesRequis;

    public function __construct(array ...$rolesRequis) {
        $this->rolesRequis = $rolesRequis;
    }

    public function process(Request $request, Handler $handler): Response
    {
        $role = $_SESSION['user_role'] ?? null;
        if ($role !== 'admin' && !in_array($role, array_merge(...$this->rolesRequis))) {
            $response = new SlimResponse();
            return $response->withHeader('Location', '/permission')->withStatus(302);
        }

        return $handler->handle($request);
    }
}