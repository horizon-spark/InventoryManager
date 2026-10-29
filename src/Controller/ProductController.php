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
        $name = (string) $request->query->get('name', '');

        $qb = $this->products->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC');

        if ($name !== '') {
            $qb->andWhere('p.name LIKE :name')
               ->setParameter('name', '%' . $name . '%');
        }

        return $this->render('product/index.html.twig', [
            'products' => $qb->getQuery()->getResult(),
            'name'     => $name,
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
}