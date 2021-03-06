<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens\Traits;

use DateTime;
use Garbuzivan\Laraveltokens\Config;
use Garbuzivan\Laraveltokens\Interfaces\GlobalTokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\GlobalToken;

trait ManagerGlobalTokenTrait
{
    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * @var GlobalTokenRepositoryInterface
     */
    protected GlobalTokenRepositoryInterface $globalTokenRepository;

    /**
     * Создать токен
     *
     * @param string        $title      - заголовок токена
     * @param DateTime|null $expiration - до когда действует токен, null - бессрочно
     * @param string|null   $token      - токен, null == автоматическая генерация токена
     *
     * @return GlobalToken
     */
    public function createGlobalToken(
        string    $title,
        ?DateTime $expiration = null,
        ?string   $token = null
    ): GlobalToken {
        $token = is_null($token) || mb_strlen($token) < 32 ?
            $this->/** @scrutinizer ignore-call */ generateToken() : $token;
        $tokenDB = $this->globalTokenRepository->createGlobalToken(
            $title,
            $expiration,
            $this->/** @scrutinizer ignore-call */ getTokenDb($token)
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
    public function deleteGlobalTokenById(int $token_id): bool
    {
        return $this->globalTokenRepository->deleteGlobalTokenById($token_id);
    }

    /**
     * Удалить токен
     *
     * @param string $token
     *
     * @return bool
     */
    public function deleteGlobalToken(string $token): bool
    {
        return $this->globalTokenRepository->deleteGlobalToken($token);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     *
     * @param int $token_id - ID токена
     *
     * @return bool
     */
    public function deactivationGlobalTokenById(int $token_id): bool
    {
        return $this->globalTokenRepository->deactivationGlobalTokenById($token_id);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     *
     * @param string $token
     *
     * @return bool
     */
    public function deactivationGlobalToken(string $token): bool
    {
        return $this->globalTokenRepository->deactivationGlobalToken($token);
    }

    /**
     * Продлить срок действия токена по id токена
     *
     * @param int           $token_id
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationGlobalTokenById(int $token_id, ?DateTime $expiration = null): bool
    {
        return $this->globalTokenRepository->prolongationGlobalTokenById($token_id, $expiration);
    }

    /**
     * Продлить срок действия токена по токену
     *
     * @param string        $token
     * @param DateTime|null $expiration
     *
     * @return bool
     */
    public function prolongationGlobalToken(string $token, ?DateTime $expiration = null): bool
    {
        return $this->globalTokenRepository->prolongationGlobalToken($token, $expiration);
    }
}
