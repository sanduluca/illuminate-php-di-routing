<?php


namespace GGbear\Routing\Middleware;

use Closure;
use GGbear\Routing\Exceptions\BasicAuthenticationException;

class BasicAuthentication
{
    /**
     * Associative array where key is login and password is value
     * @var array
     */
    protected $credentials;

    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $login = $request->header("PHP_AUTH_USER");
        $password = $request->header("PHP_AUTH_PW");

        if (empty($this->credentials[$login]) || $password !== $this->credentials[$login]) {
            throw new BasicAuthenticationException();
        }

        return $next($request);
    }
}
