<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Product;

final class SingleProductResolver implements Resolver
{
    private $name;
    private $price;

    public function __construct(string $name, int $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    public function resolve($request): Product
    {
        return new Product($this->name, $this->price);
    }
}
