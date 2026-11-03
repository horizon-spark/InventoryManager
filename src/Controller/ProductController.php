<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/products', name: 'product_')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $products,
        private readonly CategoryRepository $categories,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Список товаров (страница + фильтр по имени через ?name=).
     */
   #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $name     = (string) $request->query->get('name', '');
        $archived = $request->query->getBoolean('archived'); // ?archived=1

        $qb = $this->products->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')->addSelect('c')
            ->orderBy('p.id', 'ASC')
            ->andWhere('p.isArchived = :archived')
            ->setParameter('archived', $archived);

        if ($name !== '') {
            $qb->andWhere('p.name LIKE :name')
               ->setParameter('name', '%' . $name . '%');
        }

        return $this->render('product/index.html.twig', [
            'products' => $qb->getQuery()->getResult(),
            'name'     => $name,
            'archived' => $archived,
        ]);
    }

    /**
     * Просмотр одного товара.
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $product = $this->products->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Товар не найден');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * Создание товара через Symfony Form.
     */
    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($product);
            $this->em->flush();

            $this->addFlash('success', 'Товар успешно добавлен!');

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Редактирование товара.
     * Используется тот же ProductType, что и для создания.
     */
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // ВАЖНО: persist() не нужен — объект уже в UnitOfWork Doctrine.
            $this->em->flush();

            $this->addFlash('success', 'Товар успешно обновлён!');

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * Удаление товара.
     * Только POST + проверка CSRF-токена.
     */
    #[Route('/{id}', name: 'delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Product $product): Response
    {
        $token = (string) $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete_product_' . $product->getId(), $token)) {
            $this->addFlash('danger', 'Неверный CSRF-токен. Удаление отменено.');
            return $this->redirectToRoute('product_list');
        }

        // Мягкое удаление: физически не удаляем — иначе нарушим FK на operation
        $product->setIsArchived(true);
        $this->em->flush();

        $this->addFlash('success', sprintf('Товар «%s» перемещён в архив.', $product->getName()));

        return $this->redirectToRoute('product_list');
    }

    /**
     * Восстановление товара из архива.
     */
    #[Route('/{id}/restore', name: 'restore', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function restore(Request $request, Product $product): Response
    {
        $token = (string) $request->request->get('_token');

        if (!$this->isCsrfTokenValid('restore_product_' . $product->getId(), $token)) {
            $this->addFlash('danger', 'Неверный CSRF-токен.');
            return $this->redirectToRoute('product_list');
        }

        $product->setIsArchived(false);
        $this->em->flush();

        $this->addFlash('success', sprintf('Товар «%s» восстановлен.', $product->getName()));

        return $this->redirectToRoute('product_list');
    }
}