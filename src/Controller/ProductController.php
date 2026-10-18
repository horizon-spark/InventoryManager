<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products', name: 'product_')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Список товаров из БД.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $items = array_map(
            fn (Product $p) => $this->toArray($p),
            $this->products->findBy([], ['id' => 'ASC'])
        );

        return new JsonResponse([
            'status' => 'success',
            'count'  => count($items),
            'data'   => $items,
        ]);
    }

    /**
     * Один товар по {id}. 404, если нет.
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->products->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Сущность не найдена');
        }

        return new JsonResponse([
            'status' => 'success',
            'data'   => $this->toArray($product),
        ]);
    }

    /**
     * Фильтрация через GET-параметры.
     * Пример: /api/products/filter?name=ноут
     */
    #[Route('/filter', name: 'filter', methods: ['GET'])]
    public function filter(Request $request): JsonResponse
    {
        $name = (string) $request->query->get('name', '');

        $qb = $this->products->createQueryBuilder('p')->orderBy('p.id', 'ASC');
        if ($name !== '') {
            $qb->andWhere('p.name LIKE :name')->setParameter('name', '%' . $name . '%');
        }

        $items = array_map(
            fn (Product $p) => $this->toArray($p),
            $qb->getQuery()->getResult()
        );

        return new JsonResponse([
            'status'  => 'success',
            'filters' => ['name' => $name],
            'data'    => $items,
        ]);
    }

    /**
     * Создание товара. Только POST, возвращает 201.
     * Body: {"name":"...", "sku":"...", "price":"123.45", "category_id":1}
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);
        if (!is_array($payload)) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => 'Некорректный JSON',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        foreach (['name', 'sku', 'price', 'category_id'] as $field) {
            if (empty($payload[$field])) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => sprintf('Поле "%s" обязательно', $field),
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        // Категория должна существовать (FK NOT NULL)
        $category = $this->categories->find((int) $payload['category_id']);
        if (!$category instanceof Category) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => sprintf('Категория #%d не найдена', (int) $payload['category_id']),
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        // SKU уникален — проверяем заранее, чтобы отдать 409, а не 500
        if ($this->products->findOneBy(['sku' => $payload['sku']])) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => 'Товар с таким SKU уже существует',
            ], JsonResponse::HTTP_CONFLICT);
        }

        $product = (new Product())
            ->setName((string) $payload['name'])
            ->setSku((string) $payload['sku'])
            ->setPrice((string) $payload['price'])
            ->setCategory($category);

        $this->em->persist($product);
        $this->em->flush();

        return new JsonResponse([
            'status'  => 'created',
            'message' => 'Товар успешно создан',
            'data'    => $this->toArray($product),
        ], JsonResponse::HTTP_CREATED);
    }

    // ---------- helpers ----------

    private function toArray(Product $p): array
    {
        return [
            'id'       => $p->getId(),
            'name'     => $p->getName(),
            'sku'      => $p->getSku(),
            'price'    => $p->getPrice(),
            'category' => $p->getCategory() ? [
                'id'   => $p->getCategory()->getId(),
                'name' => $p->getCategory()->getName(),
            ] : null,
        ];
    }
}