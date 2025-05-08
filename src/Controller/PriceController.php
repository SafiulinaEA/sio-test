<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use App\DTO\CalculatePriceRequest;
use App\DTO\PurchaseRequest;
use App\Service\PriceCalculatorService;
use App\Service\PurchaseService;


class PriceController extends AbstractController
{
    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(
            Request $request,
            ValidatorInterface $validator,
            SerializerInterface $serializer,
            PriceCalculatorService $calculator
    ): JsonResponse {

//        $dto = $serializer->deserialize($request->getContent(), CalculatePriceRequest::class, 'json');
//        $errors = $validator->validate($dto);
//
//        if (count($errors) > 0) {
//            return new JsonResponse(['errors' => (string) $errors], 400);
//        }
//
//        $total = $calculator->calculate($dto->products, $dto->coupon);
//        return new JsonResponse(['total' => $total]);

        $data = json_decode($request->getContent(), true);

        $dto = new CalculatePriceRequest($data);
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json(['errors' => (string)$errors], 400);
        }

        $price = $calculator->calculate($dto);

        return $this->json(['price' => $price]);
    }

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
            Request $request,
            ValidatorInterface $validator,
            PriceCalculatorService $calculator,
            PaymentProcessorResolver $processorResolver
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $dto = new PurchaseRequest();
        $dto->products = $data['products'] ?? [];
        $dto->taxNumber = $data['taxNumber'] ?? '';
        $dto->paymentProcessor = $data['paymentProcessor'] ?? '';
        $dto->coupon = $data['coupon'] ?? null;

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $amount = $calculator->calculate($dto->products, $dto->coupon, $dto->taxNumber);
        $processor = $processorResolver->resolve($dto->paymentProcessor);

        if (!$processor->pay($amount)) {
            return $this->json(['status' => 'fail', 'message' => 'Платёж не прошёл'], 500);
        }

        return $this->json(['status' => 'success', 'amount' => $amount]);
    }
}
