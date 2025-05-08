<?php

namespace App\Tax;

class ItalyTaxCalculator implements TaxCalculatorInterface
{
    public function supports(string $taxNumber): bool
    {
        return preg_match('/^IT\d{11}$/', $taxNumber);
    }

    public function calculate(float $price): int
    {
        return $price * 0.22;
    }
}