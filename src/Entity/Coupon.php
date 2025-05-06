<?php

namespace App\Entity;

use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 20)]
    private string $discountType; // 'fixed' or 'percent'

    #[ORM\Column(type: 'float')]
    private float $discountValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getDiscountType(): string
    {
        return $this->discountType;
    }

    public function setDiscountType(string $type): self
    {
        $this->discountType = $type;
        return $this;
    }

    public function getDiscountValue(): float
    {
        return $this->discountValue;
    }

    public function setDiscountValue(float $value): self
    {
        $this->discountValue = $value;
        return $this;
    }
}
