<?php

namespace App\Repository;

use App\Entity\AcademicCareer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AcademicCareer|null find($id, $lockMode = null, $lockVersion = null)
 * @method AcademicCareer|null findOneBy(array $criteria, array $orderBy = null)
 * @method AcademicCareer[]    findAll()
 * @method AcademicCareer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AcademicCareerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AcademicCareer::class);
    }

    public function findCareerBy($pathway, $grade)
    {
        return $this->createQueryBuilder('c')
            ->join('c.grade', 'g')
            ->join('c.pathway', 'p')
            ->leftJoin('c.specialism', 's')
            ->andWhere('p.name = :pathway')
            ->andWhere('g.name = :grade')
            ->addSelect( 'g', 's')
            ->setParameter('pathway', $pathway)
            ->setParameter('grade', $grade)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findCareerBySpecialism($pathway, $grade, $specialism)
    {

       return $this->createQueryBuilder('c')
            ->join('c.grade', 'g')
            ->join('c.pathway', 'p')
            ->join('c.specialism', 's')
            ->andWhere('p.name = :pathway')
            ->andWhere('g.name = :grade')
            ->andWhere('s.id = :specialism')
            ->addSelect( 'g', 's')
            ->setParameter('pathway', $pathway)
            ->setParameter('grade', $grade)
            ->setParameter('specialism', $specialism)
            ->getQuery()
            ->getOneOrNullResult();

    }

    public function findCareerByProfession($pro)
    {
        return $this->createQueryBuilder('c')
            ->join('c.profession', 'p')
            ->addSelect('p')
            ->andWhere('p.name = :pro')
            ->setParameter('pro', $pro)
            ->getQuery()
            ->getSingleResult();
    }

    public function findCareerById($career)
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.profession', 'pro')
            ->leftJoin('c.grade', 'g')
            ->leftJoin('c.pathway', 'path')
            ->leftJoin('c.specialism', 'spe')
            ->addSelect('pro', 'g', 'path', 'spe')
            ->andWhere('c.id = :career')
            ->setParameter('career', $career)
            ->getQuery()
            ->getSingleResult();
    }

}
