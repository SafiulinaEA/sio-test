<?php

namespace App\Service;

use App\Payment\PaymentProcessorResolver;

class PurchaseService
{
    public function __construct(
            private PriceCalculatorService $priceCalculator,
            private PaymentProcessorResolver $resolver
    ) {}

    public function purchase(int $product, ?string $couponCode, string $taxNumber, string $processorName): bool
    {
        $total = $this->priceCalculator->calculate($product, $couponCode, $taxNumber);

        $processor = $this->resolver->resolve($processorName);

        return $processor->pay($total);
    }
}