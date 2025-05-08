<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CalculatePriceRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    public array $products;

    #[Assert\Type('string')]
    public ?string $coupon = null;
}