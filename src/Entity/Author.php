<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * Author entity
 *
 * @ORM\Table(name="author")
 * @ORM\Entity
 */
class Author implements TranslatableInterface, EntityToArrayInterface
{
    use TranslatableTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    public function getId(): int
    {
        return $this->id;
    }

    public function toArray(string $lang = 'ru'): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->translate($lang)->getName(),
        ];
    }
}
