<?php

declare(strict_types=1);

namespace App\Helper;

use App\Entity\UserWallet;
use App\Entity\UserWalletTransaction;
use App\Repository\UserWalletRepository;
use App\Repository\UserWalletTransactionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;

class PaymentService
{
    public const TRANSACTION_TYPE_DEBIT = 'DEBIT';
    public const TRANSACTION_TYPE_CREDIT = 'CREDIT';
    public const ALLOWED_TRANSACTION_TYPES = [
        self::TRANSACTION_TYPE_DEBIT,
        self::TRANSACTION_TYPE_CREDIT,
    ];

    public const TRANSACTION_REASON_STOCK = 'STOCK';
    public const TRANSACTION_REASON_TRANSFER = 'TRANSFER';
    public const TRANSACTION_REASON_REFUND = 'REFUND';
    public const ALLOWED_TRANSACTION_REASONS = [
        self::TRANSACTION_REASON_STOCK,
        self::TRANSACTION_REASON_TRANSFER,
        self::TRANSACTION_REASON_REFUND,
    ];

    private CurrencyService $currencyService;
    private UserWalletRepository $userWalletRepository;
    private UserWalletTransactionRepository $userWalletTransactionRepository;

    public function __construct(
        CurrencyService $currencyService,
        UserWalletRepository $userWalletRepositoryy,
        UserWalletTransactionRepository $userWalletTransactionRepositoryy
    ) {
        $this->currencyService = $currencyService;
        $this->userWalletRepository = $userWalletRepositoryy;
        $this->userWalletTransactionRepository = $userWalletTransactionRepositoryy;
    }

    public function transact(UserWallet $wallet, Request $request): UserWalletTransaction
    {
        $type = TypeCaster::asNullableString($request->request->get('type'));
        $reason = TypeCaster::asNullableString($request->request->get('reason'));
        $exchanged = $this->currencyService->exchange(
            $request->request->get('amount'),
            TypeCaster::asString($request->request->get('currency')),
            $wallet->getCurrencyCode(),
        );

        $change = $this->getMoneyHelper($exchanged);
        $wallet = $this->updateBalance($wallet, $change, $type);
        $transaction = $this->createTransaction($wallet, $change, $type, $reason);

        $this->userWalletTransactionRepository->flush();

        return $transaction;
    }

    private function updateBalance(
        UserWallet $wallet,
        MoneyHelper $change,
        string $type
    ): UserWallet {
        $current = $this->getMoneyHelper($wallet->getAmount());
        $newAmount = self::TRANSACTION_TYPE_DEBIT === $type ? $current->add($change) : $current->subtract($change);
        $wallet->setAmount($newAmount);
        $this->userWalletRepository->add($wallet, false);

        return $wallet;
    }

    private function createTransaction(
        UserWallet $wallet,
        MoneyHelper $change,
        string $type,
        string $reason
    ): UserWalletTransaction {
        $dateTime = new DateTimeImmutable();

        $transaction = new UserWalletTransaction($this);
        $transaction->setAmount($change);
        $transaction->setType($type);
        $transaction->setReason($reason);
        $transaction->setUserWallet($wallet);
        $transaction->setCreatedAt($dateTime);

        $this->userWalletTransactionRepository->add($transaction, false);

        return $transaction;
    }

    public function getMoneyHelper($amount): MoneyHelper
    {
        return new MoneyHelper($amount);
    }

    public function assertTransactionType(string $type): void
    {
        if (!\in_array($type, self::ALLOWED_TRANSACTION_TYPES)) {
            throw new Exception(\sprintf('Not supported transaction type %s', $type));
        }
    }

    public function assertTransactionReason(string $reason): void
    {
        if (!\in_array($reason, self::ALLOWED_TRANSACTION_REASONS)) {
            throw new Exception(\sprintf('Not supported transaction reason %s', $reason));
        }
    }
}
