<?php

namespace App\Payment;

use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

class PaypalProcessorAdapter implements PaymentProcessorInterface
{
    public function __construct(private PaypalPaymentProcessor $processor) {}

    public function supports(string $name): bool
    {
        return strtolower($name) === 'paypal';
    }

    public function pay(int|float $price): bool
    {
        try {
            $this->processor->pay((int) $price);
            return true;
        } catch (\Throwable $e) {
            // Can be logged: $e->getMessage()
            return false;
        }
    }
}
