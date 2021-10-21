<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Carbon\Carbon;
use DateTime;
use Garbuzivan\Laraveltokens\Exceptions\TokenIsNotNalidException;
use Garbuzivan\Laraveltokens\Exceptions\UserNotExistsException;
use Garbuzivan\Laraveltokens\Interfaces\TokenRepositoryInterface;
use Garbuzivan\Laraveltokens\Models\Token;
use Illuminate\Support\Str;

class TokenManager
{
    /**
     * @var Config $config
     */
    protected Config $config;

    /**
     * @var TokenRepositoryInterface
     */
    protected TokenRepositoryInterface $TokenRepository;

    /**
     * Configuration constructor.
     * @param Config $config
     * @param TokenRepositoryInterface $TokenRepository
     */
    public function __construct(Config $config, TokenRepositoryInterface $TokenRepository)
    {
        $this->config = $config;
        $this->TokenRepository = $TokenRepository;
    }

    /**
     * Авторизация по токену
     * @param string $token
     * @return Token
     * @throws TokenIsNotNalidException
     */
    public function auth(string $token): Token
    {
        $token = $this->config->isEncryption() ? $this->getHash($token) : $token;
        $tokenDb = $this->TokenRepository->getByToken($token);
        if (is_null($tokenDb) || !$tokenDb->isValid()) {
            throw new TokenIsNotNalidException;
        }
        if ($this->config->isLastUse()) {
            $this->TokenRepository->setLastUse($token);
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
        $tokenInfo = $this->TokenRepository->getByToken($token);
        if (is_null($tokenInfo) || !$tokenInfo->isValid()) {
            return false;
        }
        return true;
    }

    /**
     * Создать токен
     * @param string $title - заголовок токена
     * @param DateTime|null $expiration - до когда действует токен
     * @param null|int $user_id - ID пользователя или null для глобального токена
     * @param string|null $token - токен, если null - создается автоматически
     * @return Token
     * @throws UserNotExistsException
     */
    public function create(
        string    $title,
        ?DateTime $expiration = null,
        ?int      $user_id = null,
        string    $token = null
    ): Token
    {
        if (is_null($expiration)) {
            $expiration = is_null($expiration) ? Carbon::now()->addYears() : $expiration;
        }
        if (is_null($token) || mb_strlen($token) < 32) {
            $token = is_null($token) ? $this->generateToken() : $token;
        }
        $this->TokenRepository->getUser($user_id);
        $tokenDB = $this->TokenRepository->create($title, $expiration, $user_id, $this->getTokenDb($token));
        $tokenDB->token = $token;
        return $tokenDB;
    }

    /**
     * Генерация случайного токена на основе даты и случайной строки
     * @return string
     */
    public function generateToken(): string
    {
        return sha1(time() . Str::random());
    }

    /**
     * Преобразование токена для БД в зависимости от настройки Encryption
     * @param string $token
     * @return string
     */
    public function getTokenDb(string $token): string
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
        return strcmp($this->getTokenDb($token), $hash) !== 0;
    }
}
