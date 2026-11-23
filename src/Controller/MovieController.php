<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{
    // 1. Liste du catalogue depuis la base de données
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

    // 2. Création d'un nouveau film (placé AVANT /catalogue/{id})
    #[Route('/catalogue/nouveau', name: 'app_movie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($movie);
            $em->flush();

            $this->addFlash('success', 'Le film « ' . $movie->getTitle() . ' » a bien été ajouté au catalogue.');

            return $this->redirectToRoute('app_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movie/new.html.twig', [
            'form' => $form,
        ]);
    }

    // 3. Détail d'un film via Entity ValueResolver
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

    // 4. Édition d'un film existant
    #[Route(
        '/catalogue/{id}/modifier',
        name: 'app_movie_edit',
        requirements: ['id' => '\d+'],
        methods: ['GET', 'POST']
    )]
    public function edit(Movie $movie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Inutile d'appeler persist() : Doctrine surveille déjà $movie
            $em->flush();

            $this->addFlash('success', 'Les modifications du film « ' . $movie->getTitle() . ' » ont été enregistrées.');

            return $this->redirectToRoute('app_movie_show', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movie/edit.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    // 5. API JSON
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