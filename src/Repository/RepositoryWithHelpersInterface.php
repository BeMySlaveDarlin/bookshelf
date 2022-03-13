<?php

declare(strict_types=1);

namespace App\Repository;

interface RepositoryWithHelpersInterface
{
    public function toArray(array $entities, string $lang = 'ru'): array;

    public function flush(): void;
}
