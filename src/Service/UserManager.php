<?php

namespace App\Service;

use App\Entity\Puzzle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordEncoderInterface $passwordEncoder,
    ) {
    }

    public function createUser($email, $plainPassword)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $user->setScore($_ENV['STARTING_USER_SCORE']);
        $user->setIsPuzzleFinished(false);

        $puzzle = $this->entityManager->getRepository(Puzzle::class)->findOneBy(['id' => 1]);
        $user->setPuzzle($puzzle);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function changePassword(User $user, $plainPassword)
    {
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);
        $user->setRecoveryKey(null);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser($user, $newScore, $newPuzzleId, $isPuzzleFinished, $newPassword)
    {
        if ($newScore != '') {
            $newScore = intval($newScore);
            $user->setScore($newScore);
        }

        if ($newPuzzleId != '') {
            $puzzle = $this->entityManager->getRepository(Puzzle::class)->findOneBy(['id' => $newPuzzleId]);
            $user->setPuzzle($puzzle);
        }

        if ($isPuzzleFinished != '') {
            $isPuzzleFinished = $isPuzzleFinished === 'true' ? true : false;
            $user->setIsPuzzleFinished($isPuzzleFinished);
        }

        if ($newPassword != '') {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $newPassword));
        }

        $this->entityManager->flush();

        return $user;
    }
}
