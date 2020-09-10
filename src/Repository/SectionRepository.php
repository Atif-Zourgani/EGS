<?php

namespace App\Repository;

use App\Entity\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Section::class);
    }

    /**
     * Return all sections + students and grade
     *
     * @return array
     */
    public function findAllSectionsOrderByName() {

        return $this->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Return all sections + students and grade
     *
     * @return array
     */
    public function findAllSections() {

        return $this->createQueryBuilder('s')
            ->leftJoin('s.students', 'stu')
            ->leftJoin('s.grade', 'g')
            ->addSelect('stu', 'g')
            ->orderBy('s.name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }


    /**
     * Return all sections with roll calls
     *
     * @return mixed
     */
    public function findAllSectionsWithRollCall() {

        return $this->createQueryBuilder('s')
            ->innerJoin('s.rollCalls', 'r')
            ->andWhere("r IS NOT NULL")
            ->getQuery()
            ->getResult();
    }


    /**
     * Return section + grade where name is in wildcard
     *
     * @param $query
     * @return mixed
     */
    public function findByQuery($query) {

        return $this->createQueryBuilder('s')
            ->select('s')
            ->innerJoin('s.grade', 'g')
            ->addSelect('g')
            ->where('s.name LIKE :query or s.shortname LIKE :query')
            ->orWhere('g.name LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
    }


    /**
     * Return section with its shortname
     *
     * @param $sectionName
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findSectionBy($sectionName) {

        return $this->createQueryBuilder('s')
            ->where('s.shortname = :sectionName')
            ->setParameter('sectionName', $sectionName)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
