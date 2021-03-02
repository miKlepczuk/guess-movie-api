<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *   * @Route("/api", name="puzzle_api")
 */
class PuzzleController extends AbstractController
{
    #[Route('/puzzle', name: 'puzzle')]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PuzzleController.php',
        ]);
    }
}
