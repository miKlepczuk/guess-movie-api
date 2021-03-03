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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

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

    /**
     * @Route("/puzzles", name="puzzles", methods={"GET"})
     * 
     * @OA\Response(
     *      response=200,
     *      description="Returns all puzzles",
     * )
     * 
     * @OA\Tag(name="puzzles")
     */
    public function getPuzzles(): JsonResponse
    {
        $puzzles = $this->puzzleRepository->findAll();
        $data = [];

        foreach ($puzzles as $puzzle) {
            $data[] = $puzzle->toArray();
        }
        return new JsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @Route("/puzzles", name="add_puzzle", methods={"POST"})
     * 
     * @OA\Response(
     *  response=201,
     *  description="Creates new puzzle",
     * )
     * 
     *  @OA\Parameter(
     *     name="sentence",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     *  
     * @OA\Parameter(
     *     name="image",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * 
     * @OA\Tag(name="puzzles")
     */
    public function addPuzzle(Request $request): JsonResponse
    {
        try {
            $sentence = $request->query->get('sentence');
            $image = $request->query->get('image');

            if (!$request || !$sentence || !$image) {
                throw new \Exception;
            }
            $this->puzzleRepository->savePuzzle($sentence, $image);
            return new JsonResponse(['status' => 'Puzzle added successfully!'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Data no valid!'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @Route("/puzzles/{id}", name="get_puzzle", methods={"GET"})
     * 
     * @OA\Response(
     *      response=200,
     *      description="Returns all information about single puzzle",
     *      @Model(type=Puzzle::class)
     * )
     *
     * @OA\Tag(name="puzzles")
     */
    public function getPuzzle(int $id)
    {
        try {
            $puzzle = $this->puzzleRepository->findOneBy(['id' => $id]);
            if (!$puzzle) {
                throw new \Exception;
            }
            $data = $puzzle->toArray();
            return new JsonResponse($data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Puzzle not found!'], Response::HTTP_NOT_FOUND);
        }
    }


    /**
     * @Route("/puzzles/{id}", name="update_puzzle", methods={"PUT"})
     * @OA\Response(
     *      response=200,
     *      description="Updates the puzzle",
     * )
     * 
     * @OA\Parameter(
     *     name="sentence",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * 
     *  @OA\Parameter(
     *     name="image",
     *     in="query",
     *     @OA\Schema(type="string")
     *  )
     * 
     * @OA\Tag(name="puzzles")
     */
    public function updatePuzzle($id, Request $request): JsonResponse
    {
        try {
            $puzzle = $this->puzzleRepository->findOneBy(['id' => $id]);

            $sentence = $request->query->get('sentence');
            $image = $request->query->get('image');

            if (!$request || !$puzzle || !$sentence || !$image) {
                throw new \Exception;
            }
            $puzzle->setSentence($sentence);
            $puzzle->setImage($image);
            $updatedPuzzle = $this->puzzleRepository->updatePuzzle($puzzle);
            return new JsonResponse($updatedPuzzle->toArray(), Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Puzzle not found or data no valid!'], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @Route("/puzzles/{id}", name="delete_puzzle", methods={"DELETE"})
     * 
     * @OA\Response(
     *      response=200,
     *      description="Delets the puzzle",
     * )
     * 
     * @OA\Tag(name="puzzles")
     */
    public function deletePuzzle($id): JsonResponse
    {
        try {
            $puzzle = $this->puzzleRepository->findOneBy(['id' => $id]);

            if (!$puzzle) {
                throw new \Exception;
            }

            $this->puzzleRepository->removePuzzle($puzzle);
            return new JsonResponse(['status' => 'Puzzle deleted successfully!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'Puzzle not found!'], Response::HTTP_NOT_FOUND);
        }
    }
}
