<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\TokenIsNotNalidException;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\AccessToken;
use Illuminate\Support\Str;

class TokenManager
{
    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * @var AccessTokenRepositoryInterface
     */
    protected AccessTokenRepositoryInterface $accessTokenRepository;

    /**
     * Configuration constructor.
     * @param Config $config
     * @param AccessTokenRepositoryInterface $TokenRepository
     */
    public function __construct(Config $config, AccessTokenRepositoryInterface $TokenRepository)
    {
        $this->config = $config;
        $this->accessTokenRepository = $TokenRepository;
    }

    /**
     * Авторизация по токену
     * @param string $token
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
     * Проверить актуальность токена (наличие токена и дата активности)
     * @param string $token
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
     * Создать токен
     * @param string $title - заголовок токена
     * @param int $user_id - ID клиента
     * @param DateTime|null $expiration - до когда действует токен, null - бессрочно
     * @param string|null $user_type - класс полиморфной связи, null == App\Models\User
     * @param string|null $token - токен, null == автоматическая генерация токена
     * @return AccessToken
     * @throws Exceptions\UserNotExistsException
     */
    public function createAccessToken(
        string    $title,
        ?DateTime $expiration = null,
        int       $user_id,
        ?string   $user_type = null,
        ?string   $token = null
    ): AccessToken {
        $token = is_null($token) || mb_strlen($token) < 32 ? $this->generateAccessToken() : $token;
        $user_type = is_null($user_type) ? $this->getDefaultMorph() : $user_type;
        $this->accessTokenRepository->isMorph($user_id, $user_type);
        $tokenDB = $this->accessTokenRepository->createAccessToken(
            $title,
            $expiration,
            $user_id,
            $user_type,
            $this->getAccessTokenDb($token)
        );
        $tokenDB->token = $token;
        return $tokenDB;
    }

    /**
     * Удалить токен по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deleteAccessTokenById(int $token_id): bool
    {
        return $this->accessTokenRepository->deleteAccessTokenById($token_id);
    }

    /**
     * Удалить токен
     * @param string $token
     * @return bool
     */
    public function deleteAccessToken(string $token): bool
    {
        return $this->accessTokenRepository->deleteAccessToken($token);
    }

    /**
     * Удалить все токены пользователя по id пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return bool
     */
    public function deleteAccessTokenByUser(int $user_id, string $user_type): bool
    {
        return $this->accessTokenRepository->deleteAccessTokenByUser($user_id, $user_type);
    }

    /**
     * Очистить таблицу токенов
     * @return bool
     */
    public function deleteAllTokens(): bool
    {
        return $this->accessTokenRepository->deleteAllAccessToken();
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deactivationAccessTokenById(int $token_id): bool
    {
        return $this->accessTokenRepository->deactivationAccessTokenById($token_id);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     * @param string $token
     * @return bool
     */
    public function deactivationAccessToken(string $token): bool
    {
        return $this->accessTokenRepository->deactivationAccessToken($token);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по id пользователя
     *
     * @param int    $user_id
     * @param string $user_type
     *
     * @return bool
     */
    public function deactivationAccessTokenByUser(int $user_id, string $user_type): bool
    {
        return $this->accessTokenRepository->deactivationAccessTokenByUser($user_id, $user_type);
    }

    /**
     * Продлить срок действия токена по id токена
     * @param int $token_id
     * @param DateTime $expiration
     * @return bool
     */
    public function prolongationAccessTokenById(int $token_id, DateTime $expiration): bool
    {
        return $this->accessTokenRepository->prolongationAccessTokenById($token_id, $expiration);
    }

    /**
     * Продлить срок действия всех токенов по id пользователя
     *
     * @param int      $user_id
     * @param string   $user_type
     * @param DateTime $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenByUser(int $user_id, string $user_type, DateTime $expiration): bool
    {
        return $this->accessTokenRepository->prolongationAccessTokenByUser($user_id, $user_type, $expiration);
    }

    /**
     * Продлить срок действия токена по токену
     * @param string $token
     * @param DateTime $expiration
     * @return bool
     */
    public function prolongationAccessToken(string $token, DateTime $expiration): bool
    {
        return $this->accessTokenRepository->prolongationAccessToken($token, $expiration);
    }

    /**
     * Генерация случайного токена на основе даты и случайной строки
     * @return string
     */
    public function generateAccessToken(): string
    {
        return sha1(time() . Str::random());
    }

    /**
     * Преобразование токена для БД в зависимости от настройки Encryption
     * @param string $token
     * @return string
     */
    public function getAccessTokenDb(string $token): string
    {
        return $this->config->isEncryption() ? $this->getHash($token) : $token;
    }

    /**
     * Получение хэша
     * @param string $string
     * @return string
     */
    public function getHash(string $string): string
    {
        return hash('sha256', $string);
    }

    /**
     * Сравнение токена
     * @param string $token
     * @param string $hash
     * @return bool
     */
    public function isVerify(string $token, string $hash): bool
    {
        return strcmp($this->getAccessTokenDb($token), $hash) !== 0;
    }

    /**
     * Получить deault Morph
     * @return string
     */
    public function getDefaultMorph(): string
    {
        return 'App\Models\User';
    }
}
