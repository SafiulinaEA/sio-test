<?php

namespace App\Service;

use App\DTO\CalculatePriceRequest;
use App\DTO\PurchaseRequest;
use App\Entity\Product;
use App\Entity\Coupon;
use App\Repository\ProductRepository;
use App\Repository\CouponRepository;
use App\Tax\TaxCalculatorResolver;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PriceCalculatorService
{
    public function __construct(
            private ProductRepository $productRepo,
            private CouponRepository $couponRepo,
            private TaxCalculatorResolver $taxResolver
    ) {}

    /**
     * @param CalculatePriceRequest|PurchaseRequest $dto
     */
    public function calculate($dto): int
    {
        $product = $this->productRepo->find($dto->product);
        if (!$product) {
            throw new BadRequestHttpException('Product not found.');
        }

        $price = $product->getPrice(); // Price in cents

        $couponCode = $dto->couponCode ?? null;

        if (!empty($couponCode)) {
            $coupon = $this->couponRepo->findOneBy(['code' => $couponCode]);

            if (!$coupon) {
                throw new BadRequestHttpException('The coupon does not exist.');
            }

            $price = match ($coupon->getDiscountType()) {
                'percent' => (int) round($price * (1 - $coupon->getDiscountValue() / 100)),
                'fixed'   => max(0, $price - (int) $coupon->getDiscountValue()),
                default   => $price,
            };
        }

        $taxCalculator = $this->taxResolver->resolve($dto->taxNumber);
        return $taxCalculator->calculateWithTax($price);
    }
}