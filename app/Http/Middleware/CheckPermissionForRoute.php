<?php

namespace App\Http\Middleware;

use App\Exceptions\User\Permission\RouteAccessDenied;
use App\Http\Controllers\AuthController;
use App\User;
use Closure;
use App\Exceptions\User\Login\InvalidToken;

class CheckPermissionForRoute
{

    public function handle($request, Closure $next)
    {
        $uri = str_replace('api/', '', $request->path());

        $token = $request->input('access_token', null);

        try {
            $id = AuthController::currentUsedId($token);
            $user = User::find($id);

        } catch (InvalidToken $e) {
            $user = User::where('email', 'guest@mail.com')->first();
        }


        throw_if(!$user->mayDo($uri), new RouteAccessDenied());

        return $next($request);
    }
}
