<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordEncoderInterface $passwordEncoder,
    ) {
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getData() as [$email,  $plainPassword, $referencePuzzle, $roles]) {

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
            $user->setScore($_ENV['STARTING_USER_SCORE']);
            $user->setIsPuzzleFinished(false);
            $user->setPuzzle($this->getReference($referencePuzzle));
            $user->setRoles($roles);

            $manager->persist($user);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [
                'superadmin@example.com',
                '123456',
                PuzzleFixtures::FIRST_PUZZLE_REFERENCE,
                ["ROLE_SUPER_ADMIN"]
            ],
            [
                'admin@example.com',
                '123456',
                PuzzleFixtures::FIRST_PUZZLE_REFERENCE,
                ["ROLE_ADMIN"]
            ],
            [
                'user@example.com',
                '123456',
                PuzzleFixtures::FIRST_PUZZLE_REFERENCE,
                ["ROLE_USER"]
            ],
        ];
    }
}
