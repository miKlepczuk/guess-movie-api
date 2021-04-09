<?php

namespace App\Service;

use App\Entity\Puzzle;
use App\Repository\PuzzleRepository;
use Doctrine\ORM\EntityManagerInterface;

class PuzzleManager
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private PuzzleRepository $puzzleRepository
    ) {
    }

    public function savePuzzle(string $sentence, string $image)
    {
        $puzzle = new Puzzle();
        $puzzle->setSentence($sentence);
        $puzzle->setImage($image);
        $this->entityManager->persist($puzzle);
        $this->entityManager->flush();
    }

    public function updatePuzzle(Puzzle $puzzle, string $sentence, string $image): Puzzle
    {
        $puzzle->setSentence($sentence);
        $puzzle->setImage($image);
        $this->entityManager->persist($puzzle);
        $this->entityManager->flush();

        return $puzzle;
    }

    public function removePuzzle(Puzzle $puzzle)
    {
        $this->entityManager->remove($puzzle);
        $this->entityManager->flush();
    }

    public function getAllAsArray()
    {
        $puzzles = $this->puzzleRepository->findAll();
        $array = [];

        foreach ($puzzles as $puzzle) {
            $array[] = $puzzle->toArray();
        }

        return $array;
    }
}
