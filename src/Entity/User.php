<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements EntityToArrayInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity=UserWallet::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $userWallet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserWallet(): ?UserWallet
    {
        return $this->userWallet;
    }

    public function setUserWallet(UserWallet $userWallet): self
    {
        // set the owning side of the relation if necessary
        if ($userWallet->getUser() !== $this) {
            $userWallet->setUser($this);
        }

        $this->userWallet = $userWallet;

        return $this;
    }

    public function toArray(string $lang = 'ru'): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'walletId' => $this->getUserWallet()->getId(),
        ];
    }
}
