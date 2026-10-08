<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class MainController
{
    #[Route('/', name: 'api_home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'message' => 'Inventory Management API',
            'version' => '1.0',
        ]);
    }

    // #[Route('/api/entity/{id}', name: 'entity_show', methods: ['GET'])]
    // public function show(int $id): JsonResponse
    // {
    //     return new JsonResponse([
    //         'message' => 'Вы запросили сущность',
    //         'id' => $id
    //     ]);
    // }

    // #[Route('/api/search', name: 'entity_search', methods: ['GET'])]
    // public function search(Request $request): JsonResponse
    // {
    //     // Получение параметра ?query=... из URL (значение по умолчанию 'all')
    //     $searchQuery = $request->query->get('query', 'all');
        
    //     $data = [
    //         'status' => 'success',
    //         'search_term' => $searchQuery,
    //         'results' => [
    //             ['id' => 1, 'name' => 'Результат 1'],
    //             ['id' => 2, 'name' => 'Результат 2'],
    //         ]
    //     ];

    //     return new JsonResponse($data);
    // }
}