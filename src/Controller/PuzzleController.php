<?php

namespace App\Controller;

use App\Entity\Puzzle;
use App\Repository\PuzzleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *   * @Route("/api")
 */
class PuzzleController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PuzzleRepository
     */
    private $puzzleRepository;


    public function __construct(EntityManagerInterface $entityManager, PuzzleRepository $puzzleRepository)
    {
        $this->entityManager = $entityManager;
        $this->puzzleRepository = $puzzleRepository;
    }
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PuzzleController.php',
        ]);
    }
}
