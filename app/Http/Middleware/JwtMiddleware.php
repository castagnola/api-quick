<?php

namespace App\Http\Middleware;

use Closure;

use JWTAuth;

use Exception;

use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware

{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next)

    {

        try {

            $user = \JWTAuth::parseToken()->authenticate();

            if (!$user) throw new Exception('User Not Found');

        } catch (Exception $e) {

            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {

                return response()->json(["error" => "Token Invalid"]);

            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {

                return response()->json(["error" => "Token Expired"]);

            } else {

                if ($e->getMessage() === 'User Not Found') {

                    return response()->json(["error" => "User Not Found"]);
                }

                return response()->json(["error" => "Token not found"]);

            }

        }

        return $next($request);

    }

}
