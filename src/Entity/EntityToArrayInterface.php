<?php

declare(strict_types=1);

namespace App\Entity;

interface EntityToArrayInterface
{
    public function toArray(string $lang = 'ru'): array;
}
