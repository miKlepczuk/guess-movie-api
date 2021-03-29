<?php

namespace App\Repository;

use App\Entity\Puzzle;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;


    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function createUser($email, $plainPassword)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $plainPassword));
        $user->setScore(100);
        $user->setIsPuzzleFinished(false);

        $puzzle = $this->_em->getRepository(Puzzle::class)->findOneBy(['id' => 1]);
        $user->setPuzzle($puzzle);

        $this->_em->persist($user);
        $this->_em->flush();

        return $user;
    }

    public function updateUserGame($user, $newScore, $newPuzzleId, $isPuzzleFinished)
    {
        if ($newScore != '') {
            $newScore = intval($newScore);
            $user->setScore($newScore);
        }
        if ($newPuzzleId != '') {
            $puzzle = $this->_em->getRepository(Puzzle::class)->findOneBy(['id' => $newPuzzleId]);
            $user->setPuzzle($puzzle);
        }
        if ($isPuzzleFinished != '') {
            $isPuzzleFinished = $isPuzzleFinished === 'true' ? true : false;
            $user->setIsPuzzleFinished($isPuzzleFinished);
        }
        $this->_em->flush();
        return $user;
    }
}
