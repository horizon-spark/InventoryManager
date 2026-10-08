<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProductController
{
    /**
     * Список товаров (захардкоженный массив).
     */
    #[Route('/api/products', name: 'product_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $products = [
            ['id' => 1, 'name' => 'Ноутбук',  'sku' => 'NB-001', 'quantity' => 12],
            ['id' => 2, 'name' => 'Монитор',  'sku' => 'MN-002', 'quantity' => 7],
            ['id' => 3, 'name' => 'Клавиатура', 'sku' => 'KB-003', 'quantity' => 25],
        ];

        return new JsonResponse([
            'status' => 'success',
            'count' => count($products),
            'data' => $products,
        ]);
    }

    /**
     * Один товар по {id} с проверкой типа int.
     */
    #[Route('/api/products/{id}', name: 'product_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'id' => $id,
                'name' => 'Товар #' . $id,
                'sku' => 'SKU-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT),
                'quantity' => 10,
            ],
        ]);
    }

    /**
     * Фильтрация через GET-параметры (Request).
     * Пример: /api/products/filter?name=ноут&min_qty=5
     */
    #[Route('/api/products/filter', name: 'product_filter', methods: ['GET'])]
    public function filter(Request $request): JsonResponse
    {
        $name   = $request->query->get('name', '');
        $minQty = (int) $request->query->get('min_qty', 0);

        return new JsonResponse([
            'status' => 'success',
            'filters' => [
                'name'    => $name,
                'min_qty' => $minQty,
            ],
            'data' => [
                ['id' => 1, 'name' => 'Ноутбук', 'quantity' => 12],
            ],
        ]);
    }

    /**
     * Создание товара. Только POST, возвращает 201.
     */
    #[Route('/api/products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true) ?? [];

        return new JsonResponse([
            'status' => 'created',
            'message' => 'Товар успешно создан',
            'received' => $payload,
        ], JsonResponse::HTTP_CREATED);
    }
}