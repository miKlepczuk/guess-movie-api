<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/api/user", name="update_user", methods={"PATCH"})
     * @OA\Tag(name="user")
     * @return JsonResponse
     */
    public function update(Request $request, TokenStorageInterface  $tokenStorageInterface): JsonResponse
    {
        $token = $tokenStorageInterface->getToken();
        $user = $token->getUser();

        if (!$user) {
            return new JsonResponse([
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $score = $request->query->get('score');
        $puzzleId = $request->query->get('puzzleId');

        try {
            $user = $this->userRepository->updateUserGame($user, $score, $puzzleId);
            return new JsonResponse([
                'code' => Response::HTTP_OK,
                'message' => 'Changed successfully',
                'user' => [
                    'email' => $user->getEmail(),
                    'score' => $user->getScore(),
                    'puzzleId' => $user->getPuzzle()->getId(),
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad request',
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
