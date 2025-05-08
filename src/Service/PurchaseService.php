<?php

namespace App\Service;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class PurchaseService
{
    public function __construct(
            private PriceCalculatorService $priceCalculator
    ) {}

    public function purchase(array $products, ?string $coupon, string $taxNumber, string $processor): string
    {
        $total = $this->priceCalculator->calculate($products, $coupon, $taxNumber);

        return match($processor) {
            'paypal' => (new PaypalPaymentProcessor())->pay($total),
            'stripe' => (new StripePaymentProcessor())->processPayment($total),
            default => throw new \InvalidArgumentException('Unsupported processor.')
        };
    }
}