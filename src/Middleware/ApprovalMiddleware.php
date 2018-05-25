<?php

namespace TinkLabs\Bank\Middleware;

use Closure;

/**
 * Must use the following API to obtain approval prior to the transfer
 * 
 * 转帐之前，必须获得指定API批准
 */
class ApprovalMiddleware
{

    const APPROVAL_URL = 'http://handy.travel/test/success.json';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        return $next($request);
    }
}
