<?php

namespace App\Payment;

class PaymentProcessorResolver
{
    /**
     * @param iterable<PaymentProcessorInterface> $processors
     */
    public function __construct(private iterable $processors) {}

    public function resolve(string $name): PaymentProcessorInterface
    {
        foreach ($this->processors as $processor) {
            if ($processor->supports($name)) {
                return $processor;
            }
        }

        throw new \InvalidArgumentException("Платёжный процессор '$name' не поддерживается.");
    }
}