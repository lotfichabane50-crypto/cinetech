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

class MovieController extends AbstractController
{
    #[Route('/catalogue', name: 'app_movie_index', methods: ['GET'])]
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/index.html.twig', [
            'movies' => $movieRepository->findAllWithCategory(),
        ]);
    }

    #[Route('/catalogue/nouveau', name: 'app_movie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($movie);
            $em->flush();

            $this->addFlash('success', 'Le film a bien été ajouté au catalogue.');

            return $this->redirectToRoute('app_movie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movie/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/catalogue/{id}', name: 'app_movie_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Movie $movie): Response
    {
        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/catalogue/{id}/modifier', name: 'app_movie_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Movie $movie, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('app_movie_show', ['id' => $movie->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('movie/edit.html.twig', [
            'form' => $form,
            'movie' => $movie,
        ]);
    }

    #[Route('/catalogue/{id}/supprimer', name: 'app_movie_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Movie $movie, Request $request, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');

        if ($this->isCsrfTokenValid('delete' . $movie->getId(), $token)) {
            $em->remove($movie);
            $em->flush();

            $this->addFlash('warning', 'Le film a bien été supprimé du catalogue.');
        }

        return $this->redirectToRoute('app_movie_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/catalogue', name: 'api_movie_list', methods: ['GET'])]
    public function apiList(MovieRepository $movieRepository): JsonResponse
    {
        $movies = array_map(
            static fn (Movie $movie): array => [
                'id' => $movie->getId(),
                'title' => $movie->getTitle(),
                'synopsis' => $movie->getSynopsis(),
                'duration' => $movie->getDuration(),
                'releaseDate' => $movie->getReleaseDate()?->format('Y-m-d'),
                'rating' => $movie->getRating(),
                'poster' => $movie->getPoster(),
                'category' => $movie->getCategory()?->getName(),
            ],
            $movieRepository->findAllWithCategory()
        );

        return $this->json($movies);
    }
}