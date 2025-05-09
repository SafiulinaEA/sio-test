<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupons = [
                ['P10', 10, Coupon::TYPE_PERCENT],
                ['P100', 100, Coupon::TYPE_PERCENT],
                ['D15', 1500, Coupon::TYPE_FIXED], //15 euro
        ];

        foreach ($coupons as [$code, $value, $type]) {
            $coupon = new Coupon();
            $coupon->setCode($code);
            $coupon->setDiscountValue($value);
            $coupon->setDiscountType($type);
            $manager->persist($coupon);
        }

        $manager->flush();
    }
}