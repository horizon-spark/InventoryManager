<?php

namespace App\Controller;

use App\Entity\Operation;
use App\Form\OperationType;
use App\Repository\OperationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/operations', name: 'operation_')]
class OperationController extends AbstractController
{
    public function __construct(
        private readonly OperationRepository $operations,
        private readonly EntityManagerInterface $em,
    ) {}

    /**
     * Список операций.
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('operation/index.html.twig', [
            'operations' => $this->operations->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC']),
        ]);
    }

    /**
     * Просмотр одной операции.
     */
    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(int $id): Response
    {
        $operation = $this->operations->find($id);
        if (!$operation) {
            throw $this->createNotFoundException('Операция не найдена');
        }

        return $this->render('operation/show.html.twig', [
            'operation' => $operation,
        ]);
    }

    /**
     * Создание операции.
     */
    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // createdAt не в форме — проставляем автоматически
            $operation->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($operation);
            $this->em->flush();

            $this->addFlash('success', 'Операция успешно добавлена!');

            return $this->redirectToRoute('operation_list');
        }

        return $this->render('operation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}