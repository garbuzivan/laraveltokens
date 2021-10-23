<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Garbuzivan\Laraveltokens\Exceptions\TokenIsNotNalidException;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Interfaces\GlobalTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\AccessToken;
use Garbuzivan\Laraveltokens\Traits\ManagerAccessTokenTrait;
use Garbuzivan\Laraveltokens\Traits\ManagerGlobalTokenTrait;
use Illuminate\Support\Str;

class TokenManager
{
    use ManagerAccessTokenTrait, ManagerGlobalTokenTrait;

    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * @var AccessTokenRepositoryInterface
     */
    protected AccessTokenRepositoryInterface $accessTokenRepository;

    /**
     * @var GlobalTokenRepositoryInterface
     */
    protected GlobalTokenRepositoryInterface $globalTokenRepository;

    /**
     * Configuration constructor.
     *
     * @param Config                         $config
     * @param AccessTokenRepositoryInterface $TokenRepository
     * @param GlobalTokenRepositoryInterface $globalTokenRepository
     */
    public function __construct(
        Config                         $config,
        AccessTokenRepositoryInterface $TokenRepository,
        GlobalTokenRepositoryInterface $globalTokenRepository
    ) {
        $this->config = $config;
        $this->accessTokenRepository = $TokenRepository;
        $this->globalTokenRepository = $globalTokenRepository;
    }

    /**
     * Авторизация по токену
     *
     * @param string $token
     *
     * @return AccessToken
     * @throws TokenIsNotNalidException
     */
    public function auth(string $token): AccessToken
    {
        $token = $this->config->isEncryption() ? $this->getHash($token) : $token;
        $tokenDb = $this->accessTokenRepository->getAccessToken($token);
        if (is_null($tokenDb) || !$tokenDb->isValid()) {
            throw new TokenIsNotNalidException;
        }
        if ($this->config->isLastUse()) {
            $this->accessTokenRepository->setLastUseAccessToken($token);
        }
        return $tokenDb;
    }

    /**
     * Очистить таблицу токенов
     *
     * @return void
     */
    public function deleteAllTokens(): void
    {
        $this->accessTokenRepository->deleteAllAccessToken();
        $this->GlobalTokenRepository->deleteAllGlobalToken();
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
     * @return string
     */
    public function generateAccessToken(): string
    {
        return sha1(time() . Str::random());
    }

    /**
     * Преобразование токена для БД в зависимости от настройки Encryption
     *
     * @param string $token
     *
     * @return string
     */
    public function getAccessTokenDb(string $token): string
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
        return strcmp($this->getAccessTokenDb($token), $hash) !== 0;
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
}
