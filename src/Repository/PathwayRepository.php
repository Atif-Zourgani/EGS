<?php

namespace App\Repository;

use App\Entity\Pathway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pathway|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pathway|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pathway[]    findAll()
 * @method Pathway[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PathwayRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pathway::class);
    }


    public function findByCareer($career)
    {
        return $this->createQueryBuilder('p')
            ->join('p.academicCareers', 'c')
            ->andWhere('c.id = :career')
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();
    }

    public function findAllPathways()
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getArrayResult();
    }
}
