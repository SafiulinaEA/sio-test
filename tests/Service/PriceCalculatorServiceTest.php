<?php

namespace App\Tests\Service;

use App\DTO\CalculatePriceRequest;
use App\Entity\Product;
use App\Entity\Coupon;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Service\PriceCalculatorService;
use App\Tax\TaxCalculatorInterface;
use App\Tax\TaxCalculatorResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PriceCalculatorServiceTest extends TestCase
{
    private ProductRepository $productRepo;
    private CouponRepository $couponRepo;
    private TaxCalculatorResolver $taxResolver;
    private TaxCalculatorInterface $taxCalculator;
    private PriceCalculatorService $service;

    protected function setUp(): void
    {
        $this->productRepo = $this->createMock(ProductRepository::class);
        $this->couponRepo = $this->createMock(CouponRepository::class);
        $this->taxCalculator = $this->createMock(TaxCalculatorInterface::class);
        $this->taxResolver = $this->createMock(TaxCalculatorResolver::class);
        $this->service = new PriceCalculatorService(
                $this->productRepo,
                $this->couponRepo,
                $this->taxResolver
        );
    }

    public function testCalculateWithoutCoupon(): void
    {
        $dto = new CalculatePriceRequest();
        $dto->product = 1;
        $dto->taxNumber = 'DE123456789';

        $product = (new Product())->setName('iPhone')->setPrice(10000);

        $this->productRepo->method('find')->willReturn($product);
        $this->taxResolver->method('resolve')->willReturn($this->taxCalculator);
        $this->taxCalculator->method('calculateWithTax')->willReturn(11900);

        $price = $this->service->calculate($dto);
        $this->assertEquals(11900, $price);
    }

    public function testCalculateWithFixedCoupon(): void
    {
        $dto = new CalculatePriceRequest();
        $dto->product = 1;
        $dto->taxNumber = 'DE123456789';
        $dto->couponCode = 'D15';

        $product = (new Product())->setName('iPhone')->setPrice(10000);

        $coupon = new Coupon();
        $coupon->setCode('D15')->setDiscountType('fixed')->setDiscountValue(1500);

        $this->productRepo->method('find')->willReturn($product);
        $this->couponRepo->method('findOneBy')->with(['code' => 'D15'])->willReturn($coupon);
        $this->taxResolver->method('resolve')->willReturn($this->taxCalculator);
        $this->taxCalculator->method('calculateWithTax')->willReturn(10200); // 10000 - 1500 = 8500 + tax

        $price = $this->service->calculate($dto);
        $this->assertEquals(10200, $price);
    }

    public function testCalculateWithUnknownCouponThrows(): void
    {
        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('The coupon does not exist.');

        $dto = new CalculatePriceRequest();
        $dto->product = 1;
        $dto->taxNumber = 'IT12345678900';
        $dto->couponCode = 'UNKNOWN';

        $product = (new Product())->setName('iPhone')->setPrice(10000);

        $this->productRepo->method('find')->willReturn($product);
        $this->couponRepo->method('findOneBy')->with(['code' => 'UNKNOWN'])->willReturn(null);

        $this->service->calculate($dto);
    }
}