<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryToArrayInterface
{
    public function toArray(array $entities, string $lang = 'ru'): array;
}
