<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Garbuzivan\Laraveltokens\Exceptions\TokenIsNotValidException;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Interfaces\GlobalTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Traits\ManagerAccessTokenTrait;
use Garbuzivan\Laraveltokens\Traits\ManagerGlobalTokenTrait;

class TokenManager
{
    use ManagerAccessTokenTrait, ManagerGlobalTokenTrait;

    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * @var Token $token
     */
    protected Token $token;

    /**
     * @var AccessTokenRepositoryInterface
     */
    protected AccessTokenRepositoryInterface $accessTokenRepository;

    /**
     * @var GlobalTokenRepositoryInterface
     */
    protected GlobalTokenRepositoryInterface $globalTokenRepository;

    /**
     * @var Coder
     */
    protected Coder $coder;

    /**
     * Configuration constructor.
     *
     * @param Config                         $config
     * @param AccessTokenRepositoryInterface $TokenRepository
     * @param GlobalTokenRepositoryInterface $globalTokenRepository
     * @param Coder                          $coder
     */
    public function __construct(
        Config                         $config,
        AccessTokenRepositoryInterface $TokenRepository,
        GlobalTokenRepositoryInterface $globalTokenRepository,
        Coder                          $coder
    ) {
        $this->config = $config;
        $this->accessTokenRepository = $TokenRepository;
        $this->globalTokenRepository = $globalTokenRepository;
        $this->coder = $coder;
        $this->token = app(Token::class);
    }

    /**
     * Авторизация по токену
     *
     * @param string $token
     *
     * @return Token
     * @throws TokenIsNotValidException
     */
    public function auth(string $token): Token
    {
        $this->token->loadTokenHeader($this->coder->decode($token, $this->config->getSalt()));
        $token = $this->config->isEncryption() ? $this->getHash($token) : $token;
        $tokenDb = $this->accessTokenRepository->getAccessToken($token);
        $this->token->load($tokenDb);
        if (!$this->token->isValid()) {
            $tokenDb = $this->globalTokenRepository->getGlobalToken($token);
            $this->token->load($tokenDb);
        }
        if (!$this->token->isValid()) {
            throw new TokenIsNotValidException;
        }
        $this->setLastUse($this->token->id);
        return $this->token;
    }

    /**
     * @param int $token_id
     */
    public function setLastUse(int $token_id): void
    {
        if (!$this->config->isLastUse()) {
            return;
        }
        $this->accessTokenRepository->setLastUseAccessToken($token_id);
        $this->globalTokenRepository->setLastUseGlobalToken($token_id);
    }

    /**
     * Очистить таблицу токенов
     *
     * @return void
     */
    public function deleteAllTokens(): void
    {
        $this->accessTokenRepository->deleteAllAccessToken();
        $this->globalTokenRepository->deleteAllGlobalToken();
    }

    /**
     * Проверить актуальность токена (наличие токена и дата активности)
     *
     * @param string $token
     *
     * @return bool
     */
    public function isValid(string $token): bool
    {
        $token = $this->config->isEncryption() ? $this->getHash($token) : $token;
        $tokenInfo = $this->accessTokenRepository->getAccessToken($token);
        if (is_null($tokenInfo) || !$tokenInfo->isValid()) {
            return false;
        }
        return true;
    }

    /**
     * Генерация случайного токена на основе даты и случайной строки
     *
     * @param array $payload
     * @param array $head
     *
     * @return string
     * @throws \Exception
     */
    public function generateToken(array $payload = [], array $head = []): string
    {
        return $this->coder->encode($payload, $this->config->getSalt(), $head);
    }

    /**
     * Преобразование токена для БД в зависимости от настройки Encryption
     *
     * @param string $token
     *
     * @return string
     */
    public function getTokenDb(string $token): string
    {
        return $this->config->isEncryption() ? $this->getHash($token) : $token;
    }

    /**
     * Получение хэша
     *
     * @param string $string
     *
     * @return string
     */
    public function getHash(string $string): string
    {
        return hash('sha256', $string);
    }

    /**
     * Сравнение токена
     *
     * @param string $token
     * @param string $hash
     *
     * @return bool
     */
    public function isVerify(string $token, string $hash): bool
    {
        return strcmp($this->getTokenDb($token), $hash) !== 0;
    }

    /**
     * Получить deault Morph
     *
     * @return string
     */
    public function getDefaultMorph(): string
    {
        return 'App\Models\User';
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }

    /**
     * Проверка полиморфной связи
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return void
     * @throws UserNotExistsException
     */
    public function isMorph(int $user_id, string $user_type): void
    {
        $user = app($user_type)->/** @scrutinizer ignore-call */ where('id', $user_id)->first();
        if (is_null($user)) {
            throw new UserNotExistsException;
        }
    }
}
