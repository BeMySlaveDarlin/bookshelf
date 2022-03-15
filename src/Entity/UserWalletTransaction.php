<?php

namespace App\Entity;

use App\Helper\MoneyHelper;
use App\Helper\PaymentService;
use App\Repository\UserWalletTransactionRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserWalletTransactionRepository::class)
 */
class UserWalletTransaction implements EntityToArrayInterface
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
     * @ORM\Column(type="string", length=16)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $reason;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=UserWallet::class, inversedBy="userWalletTransactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userWallet;

    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->paymentService->assertTransactionType($type);

        $this->type = $type;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->paymentService->assertTransactionReason($reason);

        $this->reason = $reason;

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserWallet(): ?UserWallet
    {
        return $this->userWallet;
    }

    public function setUserWallet(?UserWallet $userWallet): self
    {
        $this->userWallet = $userWallet;

        return $this;
    }

    public function toArray(string $lang = 'ru'): array
    {
        return [
            'id' => $this->getId(),
            'amount' => $this->getAmount(),
            'type' => $this->getType(),
            'reason' => $this->getReason(),
            'created' => $this->getCreatedAt(),
            'wallet' => $this->getUserWallet()->toArray(),
        ];
    }
}
