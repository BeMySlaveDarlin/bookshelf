<?php

declare(strict_types=1);

namespace App\Helper;

use Money\Currency;
use Money\Money;

class MoneyHelper
{
    public const DEFAULT_MULTIPLY_VALUE = 1000;
    private Money $money;

    public function __construct($amount, string $currencyCode = CurrencyService::CURRENCY_RUB)
    {
        $intAmount = TypeCaster::asInt(TypeCaster::asFloat($amount) * self::DEFAULT_MULTIPLY_VALUE);
        $this->money = new Money($intAmount, new Currency($currencyCode));
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function getAmount(): int
    {
        return TypeCaster::asInt($this->money->getAmount());
    }

    public function getReal(): float
    {
        return TypeCaster::asFloat($this->money->divide(self::DEFAULT_MULTIPLY_VALUE)->getAmount());
    }

    public function add(MoneyHelper $money): MoneyHelper
    {
        $this->money = $this->money->add($money->getMoney());

        return $this;
    }

    public function subtract(MoneyHelper $money): MoneyHelper
    {
        $this->money = $this->money->subtract($money->getMoney());

        return $this;
    }

    public function divide(int $value): MoneyHelper
    {
        $this->money = $this->money->divide($value);

        return $this;
    }

    public function multiply(int $value): MoneyHelper
    {
        $this->money = $this->money->multiply($value);

        return $this;
    }
}
