<?php

namespace App\Repository;

use App\Entity\PathwaySpecialism;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PathwaySpecialism|null find($id, $lockMode = null, $lockVersion = null)
 * @method PathwaySpecialism|null findOneBy(array $criteria, array $orderBy = null)
 * @method PathwaySpecialism[]    findAll()
 * @method PathwaySpecialism[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathwaySpecialismRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PathwaySpecialism::class);
    }

    public function findAllWithCareer($career)
    {
        return $this->createQueryBuilder('s')
            ->join('s.academicCareers', 'c')
            ->addSelect('c')
            ->join('c.grade', 'g')
            ->join('g.academicCareers', 'ga')
            ->join('c.pathway', 'p')
            ->join('p.academicCareers', 'pa')
            ->andWhere('pa.id = :career')
            ->andWhere('ga.id = :career')
            ->setParameter('career', $career)
            ->getQuery()
            ->getArrayResult()
        ;
    }

}
