<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories', name: 'category_')]
class CategoryController extends AbstractController
{
    public function __construct(
        private readonly CategoryRepository $categories,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Список категорий.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $this->categories->findBy([], ['id' => 'ASC']),
        ]);
    }

    /**
     * Просмотр одной категории.
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $category = $this->categories->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Категория не найдена');
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * Создание категории.
     */
    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'Категория успешно добавлена!');

            return $this->redirectToRoute('category_list');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}