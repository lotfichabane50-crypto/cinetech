<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{
    // 1. Liste du catalogue avec paramètre GET optionnel (?search=...)
    #[Route('/catalogue', name: 'app_movie_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');

return $this->render('movie/index.html.twig', [
    'controller_name' => 'MovieController',
    'search' => $search,
]);
 }

    // 2. Détail d'un film (id numérique obligatoire grâce à \d+)
    #[Route(
        '/catalogue/{id}',
        name: 'app_movie_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(int $id): Response
    {
        return new Response('Détail du film n°' . $id);
    }

    // 3. Endpoint API JSON renvoyant 3 films
    #[Route('/api/catalogue', name: 'api_movie_list', methods: ['GET'])]
    public function apiList(): JsonResponse
    {
        $movies = [
            ['id' => 1, 'title' => 'Inception', 'year' => 2010],
            ['id' => 2, 'title' => 'Interstellar', 'year' => 2014],
            ['id' => 3, 'title' => 'The Dark Knight', 'year' => 2008],
        ];

        return $this->json($movies);
    }
}