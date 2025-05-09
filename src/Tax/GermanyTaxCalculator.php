<?php

namespace App\Tax;

class GermanyTaxCalculator implements TaxCalculatorInterface
{
    public function supports(string $taxNumber): bool
    {
        return preg_match('/^DE\d{9}$/', $taxNumber);
    }

    public function calculateWithTax(int $price): int
    {
        return intdiv($price * 119, 100);
    }
}