<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MoviesController extends AbstractController
{

    public function __construct(private SerializerInterface $serializer)
    {
    }

    #[Route('api/movies', name: 'movies', methods: "GET")]
    public function index(MovieRepository $movieRepository): JsonResponse
    {
        $movies = $movieRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($movies, 'json', ['groups' => 'movie_list']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('api/movies/{id}', name: 'movie', methods: "GET")]
    public function show(Movie $movie): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($movie, 'json', ['groups' => 'movie_details']),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('api/movies/add', name: 'movie_add', methods: "POST")]
    public function post(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $author = $em->getRepository(Author::class)->find($data['author']['id']);
        if (!$author) {
            throw $this->createNotFoundException('Auteur non trouvé pour l\'ID ' . $data['author']['id']);
        }
        $movie = new Movie();
        $movie->setName($data['name']);
        $movie->setDescription($data['description']);
        $movie->setAuthor($author);
        $em->persist($movie);
        $em->flush();
        return $this->json('success');
    }

    #[Route('api/movies/delete/{id}', name: 'movie_delete', methods: "DELETE")]
    public function delete(Movie $movie, MovieRepository $userRepository): JsonResponse
    {
        $userRepository->remove($movie, true);
        return $this->json($movie);
    }
}