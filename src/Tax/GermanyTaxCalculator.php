<?php

namespace App\Tax;

class GermanyTaxCalculator implements TaxCalculatorInterface
{
    public function supports(string $taxNumber): bool
    {
        return preg_match('/^DE\d{9}$/', $taxNumber);
    }

    public function calculate(float $price): int
    {
        return $price * 0.19;
    }
}