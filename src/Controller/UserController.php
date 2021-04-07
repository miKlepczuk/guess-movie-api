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
     * @Route("/api/users/{id}", name="update_user", methods={"PATCH"})
     * @OA\Tag(name="user")
     * @OA\Parameter(
     *     name="score",
     *     in="query",
     *     @OA\Schema(type="integer")
     *  )
     * @OA\Parameter(
     *     name="puzzleId",
     *     in="query",
     *     @OA\Schema(type="integer")
     *  )  
     * @OA\Parameter(
     *     name="isPuzzleFinished",
     *     in="query",
     *     @OA\Schema(type="boolean")
     *  )
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * @return JsonResponse
     */
    public function update($id, Request $request, TokenStorageInterface  $tokenStorageInterface): JsonResponse
    {
        $token = $tokenStorageInterface->getToken();
        $user = $token->getUser();

        if (!$user || $id != $user->getId()) {
            return new JsonResponse([
                'code' => Response::HTTP_FORBIDDEN,
                'message' => 'Forbidden'
            ], Response::HTTP_FORBIDDEN);
        }

        $score = $request->query->get('score');
        $puzzleId = $request->query->get('puzzleId');
        $isPuzzleFinished = $request->query->get('isPuzzleFinished');
        $newPassword = $request->query->get('password');

        if ($score == '' && $puzzleId == '' && $isPuzzleFinished == '' && $newPassword == '') {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Missing parameters',
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($newPassword != ''  && strlen($newPassword) < 6) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_CONFLICT,
                    'message' => 'Your password must be at least 6 characters long'
                ],
                Response::HTTP_CONFLICT
            );
        }

        try {
            $user = $this->userRepository->updateUser($user, $score, $puzzleId, $isPuzzleFinished, $newPassword);
            return new JsonResponse([
                'code' => Response::HTTP_OK,
                'message' => 'Changed successfully',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'score' => $user->getScore(),
                    'puzzleId' => $user->getPuzzle()->getId(),
                    'isPuzzleFinished' => $user->getIsPuzzleFinished()
                ]
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad request',
            ], Response::HTTP_BAD_REQUEST);;
        }
    }
}
