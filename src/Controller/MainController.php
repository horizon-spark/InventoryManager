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
}