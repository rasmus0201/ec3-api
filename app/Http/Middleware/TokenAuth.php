<?php

namespace App\Http\Middleware;

use App\Http\JsonResponseFactory;
use Closure;
use Illuminate\Http\Request;

class TokenAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization') !== '05Cknj50fawAOaUrDDmy2158X817BNmuDlDldQmbFHtpqJ05Iq3oitxmyrh8D82F') {
            return JsonResponseFactory::unauthorized();
        }

        return $next($request);
    }
}
