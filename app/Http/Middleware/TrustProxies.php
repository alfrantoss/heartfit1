<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Trust all proxies — required for Railway, Heroku, and other
     * platforms that sit behind a reverse proxy/load balancer.
     * Without this, Laravel cannot detect HTTPS from X-Forwarded-Proto,
     * causing session cookies to be marked secure=true but sent over
     * what Laravel thinks is HTTP → CSRF 419 errors.
     */
    protected $proxies = '*';

    protected $headers = Request::HEADER_X_FORWARDED_FOR
        | Request::HEADER_X_FORWARDED_HOST
        | Request::HEADER_X_FORWARDED_PORT
        | Request::HEADER_X_FORWARDED_PROTO
        | Request::HEADER_X_FORWARDED_AWS_ELB;
}
