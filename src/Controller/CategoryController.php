<?php
namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'category_list', methods: ['GET'])]
    public function list(CategoryRepository $categoryRepository): Response
    {
        // Получаем все записи из базы
        $categories = $categoryRepository->findAll();
        
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ];
        }

        return $this->render('category/index.html.twig', [
            'categories' => $data,
        ]);
    }

    #[Route('/categories/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        
        // Создаем форму и привязываем к ней пустой объект
        $form = $this->createForm(CategoryType::class, $category);
        
        // Обрабатываем входящий POST-запрос
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Сохраняем объект в БД (Data Mapper)
            $em->persist($category);
            $em->flush();

            // Добавляем Flash-сообщение об успехе
            $this->addFlash('success', 'Запись успешно добавлена!');
            
            // Выполняем редирект на список
            return $this->redirectToRoute('category_list');
        }

        // Рендерим шаблон, передавая в него объект представления формы
        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}