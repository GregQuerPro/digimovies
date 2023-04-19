<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('api/users', name: 'users', methods: 'GET')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->json($users);
    }

    #[Route('api/users/{id}', name: 'user', methods: "GET")]
    public function show(User $user): JsonResponse
    {
        return $this->json($user);
    }

    #[Route('api/users/add', name: 'user_add', methods: "POST")]
    public function post(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em)
    : JsonResponse
    {
        $res = json_decode($request->getContent(), true);
        $data = $res[0];
        $user = new User();
        $user->setEmail($data['email']);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();
        return $this->json($user);
    }

    #[Route('api/users/delete/{id}', name: 'user_delete', methods: "DELETE")]
    public function delete(User $user, UserRepository $userRepository): JsonResponse
    {
        $userRepository->remove($user, true);
        return $this->json($user);
    }
}
