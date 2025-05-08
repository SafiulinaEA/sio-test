<?php
namespace App\Payment;

interface PaymentProcessorInterface
{
    public function pay(int|float $price): bool;
    public function supports(string $name): bool;
}