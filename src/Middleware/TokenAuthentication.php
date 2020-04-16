<?php


namespace GGbear\Routing\Middleware;

use Closure;
use Firebase\JWT\JWT;
use GGbear\Routing\Exceptions\TokenAuthenticationException;

class TokenAuthentication
{
    /**
     * @var array
     */
    protected $algorithms;

    /**
     * @var string
     */
    protected $secretKey;

    public function __construct(string $secretKey, array $algorithms = array())
    {
        $this->algorithms = $algorithms;
        $this->secretKey = $secretKey;
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
        $matches = null;
        $jwt = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;

        if (preg_match('/^Bearer\s(\S+)$/', $jwt, $matches)) {
            $jwt = $matches[1];
        } else {
            throw new TokenAuthenticationException();
        }

        try {
            $decoded = JWT::decode($jwt, $this->secretKey, $this->algorithms);
        } catch (\Exception $e) {
            throw new TokenAuthenticationException($e->getMessage());
        }

        $request->attributes->add(['jwt' => [
            "payload" => $decoded,
            "token" => $jwt
        ]]);

        return $next($request);
    }
}
