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
     * @throws InvalidOperationException
     */
    public function subtract(self $credit): self
    {
        if ($credit->value > $this->value) {
            throw InvalidOperationException::insufficientCredit($credit->value - $this->value);
        }

        return new self($this->value - $credit->value);
    }

    public function greaterThanOrEquals(self $credit): bool
    {
        return $this->value >= $credit->value;
    }

    /**
     * @throws InvalidOperationException
     */
    private function __construct(int $value)
    {
        if ($value < 0) {
            throw InvalidOperationException::negativeCredit();
        }

        $this->value = $value;
    }
}
