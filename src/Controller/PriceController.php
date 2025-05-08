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
use App\Payment\PaymentProcessorResolver;


class PriceController extends AbstractController
{
    #[Route('/calculate-price', name: 'calculate_price', methods: ['POST'])]
    public function calculatePrice(
            Request $request,
            ValidatorInterface $validator,
            SerializerInterface $serializer,
            PriceCalculatorService $calculator
    ): JsonResponse {
        $dto = $serializer->deserialize($request->getContent(), CalculatePriceRequest::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 422);
        }

        $price = $calculator->calculate($dto);

        return $this->json(['price' => $price], 200);
    }

    #[Route('/purchase', name: 'purchase', methods: ['POST'])]
    public function purchase(
            Request $request,
            SerializerInterface $serializer,
            ValidatorInterface $validator,
            PriceCalculatorService $calculator,
            PaymentProcessorResolver $processorResolver
    ): JsonResponse {
        $dto = $serializer->deserialize($request->getContent(), PurchaseRequest::class, 'json');
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], 422);
        }

        $price = $calculator->calculate($dto);

        $processor = $processorResolver->resolve($dto->paymentProcessor);
        $processor->pay($price);

        return $this->json(['status' => 'success', 'price' => $price], 200);
    }
}
