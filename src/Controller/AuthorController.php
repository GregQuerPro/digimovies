<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AuthorController extends AbstractController
{

    public function __construct(private SerializerInterface $serializer)
    {
    }

    #[Route('/api/author', name: 'authors', methods: 'GET')]
    public function index(AuthorRepository $authorRepository): JsonResponse
    {
        $authors = $authorRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($authors, 'json'),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/author/{id}', name: 'author', methods: 'GET')]
    public function single(Author $author): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($author, 'json'),
            JsonResponse::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/author/add', name: 'author_add', methods: 'POST')]
    public function add(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $author = $this->serializer->deserialize($request->getContent(), Author::class, 'json');
        $em->persist($author);
        $em->flush();
        return $this->json($author);
    }
}
