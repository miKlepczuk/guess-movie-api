<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Context\Context;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Namshi\JOSE\JWT;
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
     * @var EntityManagerInterface
     */
    private $entityManager;

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

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $JWTManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->JWTManager = $JWTManager;
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
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
                    'message' => 'User already exists'
                ],
                Response::HTTP_CONFLICT
            );
        }

        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $password));
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(
            [
                'code' => Response::HTTP_CREATED,
                'message' => 'Created'
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
                    'message' => 'Invalid Credentials'
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $jwt = $this->JWTManager->create($user);

        return new JsonResponse([
            'code' => Response::HTTP_OK,
            'message' => 'Logged in',
            'data' => [
                'email' => $user->getEmail(),
                'token' => sprintf($jwt),
            ]
        ], Response::HTTP_OK);
    }
}
