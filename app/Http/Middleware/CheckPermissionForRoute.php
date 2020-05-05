<?php

namespace App\Http\Middleware;

use App\Exceptions\User\Permission\RouteAccessDenied;
use App\Http\Controllers\AuthController;
use App\User;
use Closure;

class CheckPermissionForRoute
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
        $uri = str_replace('api/', '', $request->path());

        if ($uri === 'user/login' || $uri === 'user/login/token') {
            return $next($request);
        }

        $token = $request->input('access_token', '');
        $id = AuthController::currentUsedId($token);
        $user = User::find($id);

        throw_if(!$user->mayDo($uri), new RouteAccessDenied());

        return $next($request);
    }
}
