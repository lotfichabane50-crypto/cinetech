<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{
    // 1. Liste du catalogue depuis la base de données (1 seule requête SQL)
    #[Route('/catalogue', name: 'app_movie_index', methods: ['GET'])]
    public function index(Request $request, MovieRepository $movieRepository): Response
    {
        $search = $request->query->get('search');
        $movies = $movieRepository->findAllWithCategory();

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
            'search' => $search,
        ]);
    }

    // 2. Détail d'un film via Entity ValueResolver (404 automatique si l'ID n'existe pas)
    #[Route(
        '/catalogue/{id}',
        name: 'app_movie_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(Movie $movie): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    // 3. API JSON branchée sur la base de données
    #[Route('/api/catalogue', name: 'api_movie_list', methods: ['GET'])]
    public function apiList(MovieRepository $movieRepository): JsonResponse
    {
        $movies = $movieRepository->findAllWithCategory();
        $data = [];

        foreach ($movies as $movie) {
            $data[] = [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'category' => $movie->getCategory()?->getName(),
                'releaseYear' => $movie->getReleaseDate()?->format('Y'),
                'rating' => $movie->getRating(),
            ];
        }

        return $this->json($data);
    }
}