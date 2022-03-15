<?php

declare(strict_types=1);

namespace App\Helper;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CurrencyService
{
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_RUB = 'RUB';
    public const ALLOWED_CURRENCIES = [
        self::CURRENCY_RUB,
        self::CURRENCY_USD,
    ];

    public function exchange($amount, string $inputCurrencyCode, string $walletCurrencyCode): float
    {
        $this->assertCurrency($inputCurrencyCode);
        $this->assertCurrency($walletCurrencyCode);

        if ($inputCurrencyCode === $walletCurrencyCode) {
            return $amount;
        }

        $amount = $inputCurrencyCode === self::CURRENCY_RUB
            ? $amount / $this->getExchangePolicy()
            : $amount * $this->getExchangePolicy();

        return round($amount, 2);
    }

    /**
     * Mock exchange policy API
     */
    public function getExchangePolicy(): int
    {
        return 100;
    }

    public function assertCurrency(string $currencyCode): void
    {
        if (!in_array($currencyCode, self::ALLOWED_CURRENCIES)) {
            throw new BadRequestException(sprintf('Not supported currency %s', $currencyCode));
        }
    }
}
