<?php

namespace App\Repository;

use App\Entity\Puzzle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Puzzle|null find($id, $lockMode = null, $lockVersion = null)
 * @method Puzzle|null findOneBy(array $criteria, array $orderBy = null)
 * @method Puzzle[]    findAll()
 * @method Puzzle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PuzzleRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Puzzle::class);
        $this->manager = $manager;
    }

    // /**
    //  * @return Puzzle[] Returns an array of Puzzle objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Puzzle
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
      */
    public function savePuzzle($sentence, $image)
    {
        $puzzle = new Puzzle();
        $puzzle->setSentence($sentence);
        $puzzle->setImage($image);
        $this->manager->persist($puzzle);
        $this->manager->flush();
    }
    public function updatePuzzle(Puzzle $puzzle): Puzzle
    {
        $this->manager->persist($puzzle);
        $this->manager->flush();

        return $puzzle;
    }

    public function removePuzzle(Puzzle $puzzle)
    {
        $this->manager->remove($puzzle);
        $this->manager->flush();
    }
}
