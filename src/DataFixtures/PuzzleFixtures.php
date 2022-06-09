<?php

namespace App\DataFixtures;

use App\Entity\Puzzle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class PuzzleFixtures extends Fixture
{
    public const FIRST_PUZZLE_REFERENCE = 'FIRST_PUZZLE_REFERENCE';



    public function load(ObjectManager $manager)
    {

        foreach ($this->getData() as [$sentence, $image, $reference]) {
            $puzzle = new Puzzle();
            $puzzle->setSentence($sentence);
            $puzzle->setImage($image);

            if ($reference != null) {
                $this->addReference($reference, $puzzle);
            }

            $manager->persist($puzzle);
        }

        $manager->flush();
    }

    private function getData(): array
    {

        return [
            [
                'First',
                'example-1.jpg',
                self::FIRST_PUZZLE_REFERENCE,
            ],
            [
                'Second',
                'example-2.jpg',
                null
            ],
            [
                'Third',
                'example-1.jpg',
                null
            ],
            [
                'Fourth',
                'example-2.jpg',
                null
            ],
            [
                'Fifth',
                'example-1.jpg',
                null
            ],
        ];
    }
}
