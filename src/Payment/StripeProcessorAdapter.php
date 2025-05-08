<?php

namespace App\Payment;

use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

class StripeProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(
            private readonly StripePaymentProcessor $processor
    ) {}

    public function supports(string $name): bool
    {
        return strtolower($name) === 'stripe';
    }

    public function pay(int|float $price): bool
    {
        //Convert the price from cents(int) to euros(float)
        $priceInEuros = $price / 100;

        return $this->processor->processPayment($priceInEuros);
    }
}
