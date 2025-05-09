<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PurchaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $product;

    #[Assert\NotBlank]
    #[Assert\Regex(
            pattern: '/^(DE\d{9}|IT\d{11}|FR[A-Z]{2}\d{9}|GR\d{9})$/',
            message: 'Incorrect tax number format.'
    )]
    public string $taxNumber;

    #[Assert\Choice(choices: ['paypal', 'stripe'], message: 'Unacceptable Payment Processor.')]
    public string $paymentProcessor;

    #[Assert\Type('string')]
    public ?string $couponCode = null;
}