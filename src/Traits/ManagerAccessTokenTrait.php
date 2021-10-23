<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Traits;

use DateTime;
use Garbuzivan\Laraveltokens\Config;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Interfaces\AccessTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\AccessToken;

trait ManagerAccessTokenTrait
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
     * Создать токен
     *
     * @param string        $title      - заголовок токена
     * @param int           $user_id    - ID клиента
     * @param DateTime|null $expiration - до когда действует токен, null - бессрочно
     * @param string|null   $user_type  - класс полиморфной связи, null == App\Models\User
     * @param string|null   $token      - токен, null == автоматическая генерация токена
     *
     * @return AccessToken
     * @throws UserNotExistsException
     */
    public function createAccessToken(
        string    $title,
        ?DateTime $expiration = null,
        int       $user_id,
        ?string   $user_type = null,
        ?string   $token = null
    ): AccessToken {
        $token = is_null($token) || mb_strlen($token) < 32 ?
            $this->/** @scrutinizer ignore-call */ generateAccessToken() : $token;
        $user_type = is_null($user_type) ? $this->/** @scrutinizer ignore-call */ getDefaultMorph() : $user_type;
        $this->accessTokenRepository->isMorph($user_id, $user_type);
        $tokenDB = $this->accessTokenRepository->createAccessToken(
            $title,
            $expiration,
            $user_id,
            $user_type,
            $this->/** @scrutinizer ignore-call */ getAccessTokenDb($token)
        );
        $tokenDB->token = $token;
        return $tokenDB;
    }

    /**
     * Удалить токен по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deleteAccessTokenById(int $token_id): bool
    {
        return $this->accessTokenRepository->deleteAccessTokenById($token_id);
    }

    /**
     * Удалить токен
     *
     * @param string $token
     *
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
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deactivationAccessTokenById(int $token_id): bool
    {
        return $this->accessTokenRepository->deactivationAccessTokenById($token_id);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     *
     * @param string $token
     *
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
     *
     * @param int           $token_id
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenById(int $token_id, ?DateTime $expiration = null): bool
    {
        return $this->accessTokenRepository->prolongationAccessTokenById($token_id, $expiration);
    }

    /**
     * Продлить срок действия всех токенов по id пользователя
     *
     * @param int           $user_id
     * @param string        $user_type
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessTokenByUser(int $user_id, string $user_type, ?DateTime $expiration = null): bool
    {
        return $this->accessTokenRepository->prolongationAccessTokenByUser($user_id, $user_type, $expiration);
    }

    /**
     * Продлить срок действия токена по токену
     *
     * @param string        $token
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationAccessToken(string $token, ?DateTime $expiration = null): bool
    {
        return $this->accessTokenRepository->prolongationAccessToken($token, $expiration);
    }
}
