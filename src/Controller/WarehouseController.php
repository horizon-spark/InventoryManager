<?php

namespace App\Controller;

use App\Entity\Warehouse;
use App\Form\WarehouseType;
use App\Repository\WarehouseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/warehouses', name: 'warehouse_')]
class WarehouseController extends AbstractController
{
    public function __construct(
        private readonly WarehouseRepository $warehouses,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Список складов.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('warehouse/index.html.twig', [
            'warehouses' => $this->warehouses->findBy([], ['id' => 'ASC']),
        ]);
    }

    /**
     * Просмотр одного склада.
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $warehouse = $this->warehouses->find($id);
        if (!$warehouse) {
            throw $this->createNotFoundException('Склад не найден');
        }

        return $this->render('warehouse/show.html.twig', [
            'warehouse' => $warehouse,
        ]);
    }

    /**
     * Создание склада.
     */
    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $warehouse = new Warehouse();
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($warehouse);
            $this->em->flush();

            $this->addFlash('success', 'Склад успешно добавлен!');

            return $this->redirectToRoute('warehouse_list');
        }

        return $this->render('warehouse/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}