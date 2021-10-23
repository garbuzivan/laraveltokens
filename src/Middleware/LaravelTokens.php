<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Middleware;

use Closure;
use Garbuzivan\Laraveltokens\Exceptions\EmptyTokenException;
use Garbuzivan\Laraveltokens\Exceptions\TokenIsNotValidException;
use Garbuzivan\Laraveltokens\TokenManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaravelTokens
{
    /**
     * The request instance.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * @var TokenManager
     */
    protected TokenManager $TokenManager;

    /**
     * @param Request $request
     * @param TokenManager $TokenManager
     */
    public function __construct(Request $request, TokenManager $TokenManager)
    {
        $this->request = $request;
        $this->TokenManager = $TokenManager;
    }

    /**
     * Обработка входящего запроса.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = $this->getTokenForRequest();
        } catch (EmptyTokenException $e) {
            abort(403);
        }
        try {
            $token = $this->TokenManager->auth($token);
        } catch (TokenIsNotValidException $e) {
            abort(403);
        }
        if ($token->user instanceof Authenticatable) {
            Auth::login($token->user);
        }
        // Если все прошло успешно, то мы пропускаем запрос дальше
        return $next($request);
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     * @throws EmptyTokenException
     */
    public function getTokenForRequest(): string
    {
        $token = $this->request->query('api_token');
        if (empty($token)) {
            $token = $this->request->input('api_token');
        }
        if (empty($token)) {
            $token = $this->request->bearerToken();
        }
        if (empty($token)) {
            throw new EmptyTokenException;
        }
        return $token;
    }
}
