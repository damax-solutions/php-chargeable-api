<?php

declare(strict_types=1);

namespace Damax\ChargeableApi;

final class Credit
{
    private $value;

    public static function blank(): self
    {
        return self::fromInteger(0);
    }

    public static function fromInteger(int $value): self
    {
        return new self($value);
    }

    public function add(self $credit): self
    {
        return new self($this->value + $credit->value);
    }

    /**
     * @throws InvalidOperation
     */
    public function subtract(self $credit): self
    {
        if ($credit->value > $this->value) {
            throw InsufficientFunds::notEnough($credit->value - $this->value);
        }

        return new self($this->value - $credit->value);
    }

    /**
     * @throws InvalidOperation
     */
    private function __construct(int $value)
    {
        if ($value < 0) {
            throw InvalidOperation::negativeCredit();
        }

        $this->value = $value;
    }
}
