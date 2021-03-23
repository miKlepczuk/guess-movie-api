<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Annotations as OA;

class AuthController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTManager;

    public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTManager = $JWTManager;
    }

    /**
     * @Route("/api/register", name="register", methods={"POST"})
     * @OA\Tag(name="user")
     * 
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * 
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * 
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $email = $request->query->get('email');
        $password = $request->query->get('password');

        $user = $this->userRepository->findOneBy(['email' => $email,]);

        if (!is_null($user)) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_CONFLICT,
                    'message' => 'This email address is already taken'
                ],
                Response::HTTP_CONFLICT
            );
        }

        $user = $this->userRepository->createUser($email, $password);
        $jwt =
            $this->JWTManager->create($user);

        return new JsonResponse(
            [
                'code' => Response::HTTP_CREATED,
                'message' => 'Created',
                'user' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'score' => $user->getScore(),
                    'puzzleId' => $user->getPuzzle()->getId(),
                    'token' => sprintf($jwt),
                ]
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * @param UserInterface $user
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user)
    {
        return new JsonResponse(['token' => $this->JWTManager->create($user)]);
    }

    /**
     * @Route("/api/login_check", name="login", methods={"GET"})
     * @OA\Tag(name="user")
     * 
     * @OA\Parameter(
     *     name="email",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * 
     * @OA\Parameter(
     *     name="password",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {

        $user = $this->userRepository->findOneBy([
            'email' => $request->query->get('email'),
        ]);
        if (!$user || !$this->passwordEncoder->isPasswordValid($user,  $request->query->get('password'))) {
            return new JsonResponse(
                [
                    'code' => Response::HTTP_UNAUTHORIZED,
                    'message' => 'The email or password did not match'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $jwt = $this->JWTManager->create($user);

        return new JsonResponse([
            'code' => Response::HTTP_OK,
            'message' => 'Logged in',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'score' => $user->getScore(),
                'puzzleId' => $user->getPuzzle()->getId(),
                'token' => sprintf($jwt),
            ]
        ], Response::HTTP_OK);
    }
}
