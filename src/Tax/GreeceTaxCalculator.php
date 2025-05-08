<?php


namespace App\Tax;

class GreeceTaxCalculator implements TaxCalculatorInterface
{
    public function supports(string $taxNumber): bool
    {
        return preg_match('/^GR\d{9}$/', $taxNumber);
    }

    public function calculate(float $price): int
    {
        return $price * 0.24;
    }
}