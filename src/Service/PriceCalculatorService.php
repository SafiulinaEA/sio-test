<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Tax\TaxCalculatorResolver;

class PriceCalculatorService
{
    public function __construct(
            private ProductRepository $productRepo,
            private CouponRepository $couponRepo,
            private TaxCalculatorResolver $taxResolver
    ) {}

    public function calculate(array $productIds, ?string $couponCode, string $taxNumber): int
    {
        $products = $this->productRepo->findBy(['id' => $productIds]);
        $price = array_reduce($products, fn($carry, $product) => $carry + $product->getPrice(), 0);

        if ($couponCode !== null) {
            $coupon = $this->couponRepo->findOneBy(['code' => $couponCode]);

            if (!$coupon) {
                throw new \InvalidArgumentException('Coupon not found.');
            }

            $price = match ($coupon->getType()) {
                'percent' => $price - intval(round($price * $coupon->getValue() / 100)),
                'fixed'   => max(0, $price - $coupon->getValue()),
                default   => throw new \RuntimeException('Unknown coupon type.'),
            };
        }

        $taxCalculator = $this->taxResolver->resolve($taxNumber);
        $tax = intval(round($taxCalculator->calculate($price)));

        return $price + $tax;
    }
}