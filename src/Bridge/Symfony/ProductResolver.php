<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Bridge\Symfony;

use Damax\ChargeableApi\Product\Product;
use Damax\ChargeableApi\Product\ProductResolutionFailed;
use Damax\ChargeableApi\Product\Resolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

final class ProductResolver implements Resolver
{
    private $products = [];

    public function addProduct(Product $product, RequestMatcherInterface $matcher): void
    {
        $this->products[$product->name()] = [$product, $matcher];
    }

    /**
     * @param Request $request
     *
     * @throws ProductResolutionFailed
     */
    public function resolve($request): Product
    {
        foreach ($this->products as list($product, $matcher)) {
            if ($matcher->matches($request)) {
                return $product;
            }
        }

        throw new ProductResolutionFailed('Unable to resolve product.');
    }
}
