<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityToArrayInterface;

trait HelpersTrait
{
    public function toArray(array $entities, string $lang = 'ru'): array
    {
        $data = [];
        foreach ($entities as $entity) {
            if (\is_subclass_of($entity, EntityToArrayInterface::class)) {
                $data[] = $entity->toArray($lang);
            } else {
                $data[] = $entity->getId();
            }
        }

        return $data;
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
