<?php

namespace App\Tax;

interface TaxCalculatorInterface
{
    public function supports(string $taxNumber): bool;

    public function calculateWithTax(int $price): int;
}