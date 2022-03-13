<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * Book entity
 *
 * @ORM\Table(name="book")
 * @ORM\Entity
 */
class Book implements TranslatableInterface, EntityToArrayInterface
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

    /**
     * @var Author[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Author", inversedBy="book", cascade={"persist"})
     */
    private $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Author[]|Collection
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    public function setAuthor(Author $author): void
    {
        if ($this->authors->contains($author)) {
            return;
        }

        $this->authors->add($author);
    }

    public function toArray(string $lang = 'ru'): array
    {
        $authors = [];
        foreach ($this->authors as $author) {
            $authors[] = $author->toArray();
        }

        return [
            'id' => $this->getId(),
            'title' => $this->translate($lang)->getTitle(),
            'authors' => $authors,
        ];
    }
}
