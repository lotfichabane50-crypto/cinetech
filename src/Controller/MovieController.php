<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{
    private const MOVIES = [
        [
            'id' => 1,
            'title' => 'Inception',
            'category' => 'Science-Fiction',
            'releaseYear' => 2010,
            'synopsis' => 'Un voleur qui s\'infiltre dans les rêves des autres pour voler leurs secrets se voit confier la mission inverse : implanter une idée.',
            'rating' => 4.8,
        ],
        [
            'id' => 2,
            'title' => 'Interstellar',
            'category' => 'Science-Fiction',
            'releaseYear' => 2014,
            'synopsis' => 'Une équipe d\'explorateurs voyage à travers un trou de ver dans l\'espace pour assurer la survie de l\'humanité.',
            'rating' => 4.9,
        ],
        [
            'id' => 3,
            'title' => 'The Dark Knight',
            'category' => 'Action',
            'releaseYear' => 2008,
            'synopsis' => 'Batman doit accepter l\'une des plus grandes épreuves psychologiques et physiques pour combattre l\'injustice face au Joker.',
            'rating' => 4.7,
        ],
        [
            'id' => 4,
            'title' => 'Dune : Deuxième Partie',
            'category' => 'Aventure',
            'releaseYear' => 2024,
            'synopsis' => 'Paul Atreides s\'unit à Chani et aux Fremen pour mener la révolte contre ceux qui ont détruit sa famille.',
            'rating' => 4.4,
        ],
        [
            'id' => 5,
            'title' => 'Parasite',
            'category' => 'Thriller',
            'releaseYear' => 2019,
            'synopsis' => 'Toute la famille de Ki-taek est au chômage et s\'intéresse de près au train de vie de la richissime famille Park.',
            'rating' => 4.6,
        ],
    ];

    // 1. Liste du catalogue
    #[Route('/catalogue', name: 'app_movie_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('search');

        return $this->render('movie/index.html.twig', [
            'movies' => self::MOVIES,
            'search' => $search,
        ]);
    }

    // 2. Détail d'un film
    #[Route(
        '/catalogue/{id}',
        name: 'app_movie_show',
        requirements: ['id' => '\d+'],
        methods: ['GET']
    )]
    public function show(int $id): Response
    {
        $selectedMovie = null;

        foreach (self::MOVIES as $movie) {
            if ($movie['id'] === $id) {
                $selectedMovie = $movie;
                break;
            }
        }

        if ($selectedMovie === null) {
            throw $this->createNotFoundException('Film introuvable');
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $selectedMovie,
        ]);
    }

    // 3. API JSON (conservée du TP 2)
    #[Route('/api/catalogue', name: 'api_movie_list', methods: ['GET'])]
    public function apiList(): JsonResponse
    {
        return $this->json(self::MOVIES);
    }
}