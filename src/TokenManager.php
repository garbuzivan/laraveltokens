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
    protected TokenRepositoryInterface $tokenRepository;

    /**
     * Configuration constructor.
     * @param Config $config
     * @param TokenRepositoryInterface $TokenRepository
     */
    public function __construct(Config $config, TokenRepositoryInterface $TokenRepository)
    {
        $this->config = $config;
        $this->tokenRepository = $TokenRepository;
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
        $tokenDb = $this->tokenRepository->getByToken($token);
        if (is_null($tokenDb) || !$tokenDb->isValid()) {
            throw new TokenIsNotNalidException;
        }
        if ($this->config->isLastUse()) {
            $this->tokenRepository->setLastUse($token);
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
        $tokenInfo = $this->tokenRepository->getByToken($token);
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
    ): Token {
        $expiration = !$expiration instanceof DateTime ? Carbon::now()->addYears() : $expiration;
        if (is_null($token) || mb_strlen($token) < 32) {
            $token = is_null($token) ? $this->generateToken() : $token;
        }
        $this->tokenRepository->getUser($user_id);
        $tokenDB = $this->tokenRepository->create($title, $expiration, $user_id, $this->getTokenDb($token));
        $tokenDB->token = $token;
        return $tokenDB;
    }

    /**
     * Удалить токен по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deleteById(int $token_id): bool
    {
        return $this->tokenRepository->deleteById($token_id);
    }

    /**
     * Удалить токен
     * @param string $token
     * @return bool
     */
    public function deleteByToken(string $token): bool
    {
        return $this->tokenRepository->deleteByToken($token);
    }

    /**
     * Удалить все токены пользователя по id пользователя
     * @param int $user_id
     * @return bool
     */
    public function deleteByUser(int $user_id): bool
    {
        return $this->tokenRepository->deleteByUser($user_id);
    }

    /**
     * Очистить таблицу токенов
     * @return bool
     */
    public function deleteAll(): bool
    {
        return $this->tokenRepository->deleteAll();
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по ID токена
     * @param int $token_id - ID токена
     * @return bool
     */
    public function deactivationById(int $token_id): bool
    {
        return $this->tokenRepository->deactivationById($token_id);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по токену
     * @param string $token
     * @return bool
     */
    public function deactivationByToken(string $token): bool
    {
        return $this->tokenRepository->deactivationByToken($token);
    }

    /**
     * Деактивировать токен (прекратить срок действия токена) по id пользователя
     * @param int $user_id
     * @return bool
     */
    public function deactivationByUser(int $user_id): bool
    {
        return $this->tokenRepository->deactivationByUser($user_id);
    }

    /**
     * Продлить срок действия токена по id токена
     * @param int $token_id
     * @param DateTime $expiration
     * @return bool
     */
    public function prolongationById(int $token_id, DateTime $expiration): bool
    {
        return $this->tokenRepository->prolongationById($token_id, $expiration);
    }

    /**
     * Продлить срок действия всех токенов по id пользователя
     * @param int $user_id
     * @param DateTime $expiration
     * @return bool
     */
    public function prolongationByUser(int $user_id, DateTime $expiration): bool
    {
        return $this->tokenRepository->prolongationByUser($user_id, $expiration);
    }

    /**
     * Продлить срок действия токена по токену
     * @param string $token
     * @param DateTime $expiration
     * @return bool
     */
    public function prolongationByToken(string $token, DateTime $expiration): bool
    {
        return $this->tokenRepository->prolongationByToken($token, $expiration);
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
