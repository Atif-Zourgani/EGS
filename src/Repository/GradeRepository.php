<?php

namespace App\Repository;

use App\Entity\Grade;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Grade|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grade|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grade[]    findAll()
 * @method Grade[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Grade::class);
    }

    /**
     * Return all section's grades with roll call
     */
    public function findAllGradesWithRollCall()
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.sections', 's')
            ->innerJoin('s.rollCalls', 'r')
            ->addSelect('s')
            ->andWhere("r IS NOT NULL")
            ->getQuery()
            ->getArrayResult();
    }

    public function findByCareer($career)
    {
        return $this->createQueryBuilder('g')
            ->join('g.academicCareers', 'c')
            ->andWhere('c.id = :career')
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllWithLevels()
    {
        return $this->createQueryBuilder('g')
            ->join('g.academicCareers', 'c')
            ->leftJoin('c.disciplineLevel', 'd')
            ->addSelect('c', 'd')
            ->getQuery()
            ->getResult();
    }

}
