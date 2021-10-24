<?php

namespace Garbuzivan\Laraveltokens\Interfaces;

interface ModelToken
{
    /**
     * Проверка валидности токена по дате
     * @return bool
     */
    public function isValid(): bool;
}
