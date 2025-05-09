<?php

namespace App\Tests\Service;

use App\Service\PurchaseService;
use App\Service\PriceCalculatorService;
use App\Payment\PaymentProcessorInterface;
use App\Payment\PaymentProcessorResolver;
use PHPUnit\Framework\TestCase;

class PurchaseServiceTest extends TestCase
{
    public function testPurchaseWithStripeProcessor(): void
    {
        $calculator = $this->createMock(PriceCalculatorService::class);
        $resolver = $this->createMock(PaymentProcessorResolver::class);
        $processor = $this->createMock(PaymentProcessorInterface::class);

        $calculator->method('calculate')->willReturn(1500); // in cents
        $processor->method('pay')->with(1500)->willReturn(true);
        $resolver->method('resolve')->with('stripe')->willReturn($processor);

        $service = new PurchaseService($calculator, $resolver);

        $result = $service->purchase(1, 'D15', 'DE123456789', 'stripe');
        $this->assertTrue($result);
    }

    public function testPurchaseWithPaypalProcessor(): void
    {
        $calculator = $this->createMock(PriceCalculatorService::class);
        $resolver = $this->createMock(PaymentProcessorResolver::class);
        $processor = $this->createMock(PaymentProcessorInterface::class);

        $calculator->method('calculate')->willReturn(9500);
        $processor->method('pay')->with(9500)->willReturn(true);
        $resolver->method('resolve')->with('paypal')->willReturn($processor);

        $service = new PurchaseService($calculator, $resolver);

        $result = $service->purchase(1, null, 'FR987654321', 'paypal');
        $this->assertTrue($result);
    }
}