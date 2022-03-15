<?php

namespace App\Entity;

use App\Helper\CurrencyService;
use App\Helper\MoneyHelper;
use App\Repository\UserWalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserWalletRepository::class)
 */
class UserWallet implements EntityToArrayInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $currencyCode;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="userWallet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=UserWalletTransaction::class, mappedBy="userWallet", orphanRemoval=true)
     */
    private $userWalletTransactions;

    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
        $this->userWalletTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return \round($this->amount / MoneyHelper::DEFAULT_MULTIPLY_VALUE, 2);
    }

    public function setAmount(MoneyHelper $amount): self
    {
        $this->amount = $amount->getAmount();

        return $this;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyService->assertCurrency($currencyCode);

        $this->currencyCode = $currencyCode;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, UserWalletTransaction>
     */
    public function getUserWalletTransactions(): Collection
    {
        return $this->userWalletTransactions;
    }

    public function addUserWalletTransaction(UserWalletTransaction $userWalletTransaction): self
    {
        if (!$this->userWalletTransactions->contains($userWalletTransaction)) {
            $this->userWalletTransactions[] = $userWalletTransaction;
            $userWalletTransaction->setUserWallet($this);
        }

        return $this;
    }

    public function removeUserWalletTransaction(UserWalletTransaction $userWalletTransaction): self
    {
        if ($this->userWalletTransactions->removeElement($userWalletTransaction)) {
            // set the owning side to null (unless already changed)
            if ($userWalletTransaction->getUserWallet() === $this) {
                $userWalletTransaction->setUserWallet(null);
            }
        }

        return $this;
    }

    public function toArray(string $lang = 'ru'): array
    {
        return [
            'id' => $this->getId(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrencyCode(),
            'userId' => $this->getUser()->getId(),
        ];
    }
}
