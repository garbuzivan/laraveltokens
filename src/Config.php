<?php

declare(strict_types=1);

namespace Garbuzivan\Laraveltokens;

use Garbuzivan\Laraveltokens\Repositories\AccessTokenRepository;

class Config
{
    /**
     * Соль для сигнатур
     * @var string
     */
    protected string $salt = 'Fo3SMqqUbrxKJMQW0sVOB4Q';

    /**
     * Название конфигурационного файла
     * @var string
     */
    protected string $configName = 'laraveltokens';

    /**
     * Хранение в токена в открытом или зашифрованном виде
     * Зашифрованный тип хранения отключает возможность просматривать токен или вносить его вручную
     * @var bool
     */
    protected bool $encryption = false;

    /**
     * Автоматическое удаление токенов с законченым сроком действия
     * @var  bool
     */
    protected bool $autoClear = true;

    /**
     * Пауза перед автоматическим удалением токенов в секундах
     * @var  int
     */
    protected int $autoClearPause = 8640000;

    /**
     * Репозиторий
     * @var string
     */
    protected string $repository = AccessTokenRepository::class;

    /**
     * Фиксация последней активности токена
     * @var  bool
     */
    protected bool $lastUse = true;

    /**
     * Режим JWT
     * @var  bool
     */
    protected bool $jwt = true;

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        $this->load();
    }

    /**
     * Загрузка данных из конфигурационного файла
     * @return $this|Config
     */
    public function load(): Config
    {
        $this->encryption = (bool)config($this->configName . '.encryption', $this->encryption);
        $this->autoClear = (bool)config($this->configName . '.auto_clear', $this->autoClear);
        $this->autoClearPause = intval(config($this->configName . '.auto_clear_pause', $this->autoClearPause));
        $this->repository = (string)config($this->configName . '.repository', $this->repository);
        $this->lastUse = (bool)config($this->configName . '.last_use', $this->lastUse);
        $this->jwt = (bool)config($this->configName . '.jwt', $this->jwt);
        $this->salt = (string)config($this->configName . '.salt', $this->salt);
        $this->salt = file_exists($this->salt) ? file_get_contents($this->salt) : $this->salt;
        return $this;
    }

    /**
     * Проверка на шифрование токена
     * @return bool
     */
    public function isEncryption(): bool
    {
        return $this->encryption;
    }

    /**
     * Проверка на автоочистку от неиспользуемых токенов
     * @return bool
     */
    public function isAutoClear(): bool
    {
        return $this->autoClear;
    }

    /**
     * Проверка на автоочистку от неиспользуемых токенов
     * @return int
     */
    public function getAutoClearPause(): int
    {
        return $this->autoClearPause;
    }

    /**
     * Репозиторий
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * Обновлять последнюю активность токена Да\Нет
     * @return bool
     */
    public function isLastUse(): bool
    {
        return $this->lastUse;
    }

    /**
     * Режим JWT
     * @return bool
     */
    public function isJwt(): bool
    {
        return $this->jwt;
    }

    /**
     * Режим JWT
     * @return string
     */
    public function getSalt(): string
    {
        return $this->salt;
    }
}
