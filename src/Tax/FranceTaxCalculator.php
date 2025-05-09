<?php

namespace App\Tax;

class FranceTaxCalculator implements TaxCalculatorInterface
{
    public function supports(string $taxNumber): bool
    {
        return preg_match('/^FR[A-Z]{2}\d{9}$/', $taxNumber);
    }

    public function calculateWithTax(int $price): int
    {
        return intdiv($price * 120, 100);
    }
}