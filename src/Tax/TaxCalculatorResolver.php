<?php

namespace App\Tax;

use RuntimeException;

class TaxCalculatorResolver
{
    /**
     * @param iterable<TaxCalculatorInterface> $calculators
     */
    public function __construct(private iterable $calculators) {}

    public function resolve(string $taxNumber): TaxCalculatorInterface
    {
        foreach ($this->calculators as $calculator) {
            if ($calculator->supports($taxNumber)) {
                return $calculator;
            }
        }

        throw new RuntimeException("No tax calculator found for tax number: $taxNumber");
    }
}