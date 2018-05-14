<?php

namespace Recapi\Middleware;

use Recapi\Models\User;

class Authentication
{

    public function __invoke($request, $response, $next)
    {
        $auth = $request->getHeader('Authorization');
        $_apikey = $auth[0];
        $apikey = substr($_apikey, strpos($_apikey, ' '));
        $apikey = trim($apikey);

        $user = new User();
        $user->apikey = $apikey;

        if ( !$user->authenticate() ) {
            return $response->withStatus(401);
        }

        $request = $request->withAttribute('user_id', $user->id);
        return $next($request, $response);
    }
}