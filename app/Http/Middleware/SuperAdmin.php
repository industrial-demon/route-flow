<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class SuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User */
        $user = $request->user();
        if ($user) {
            if (!$user->isSuperAdmin()) {
                return redirect('/dash');
            }
        }
        return $next($request);
    }
}
