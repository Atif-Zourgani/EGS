<?php

namespace App\Repository;

use App\Entity\DisciplineLevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DisciplineLevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method DisciplineLevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method DisciplineLevel[]    findAll()
 * @method DisciplineLevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DisciplineLevelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DisciplineLevel::class);
    }

    // remove
    public function findByDiscipline($discipline/*, $student*/)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        return $this->createQueryBuilder('dl')

            ->leftJoin('s.studentSkills', 'ss')
            //->leftJoin('ss.student', 'st')
            //->join('s.disciplineLevel', 'l')
            ->join('dl.discipline', 'd')
            ->where('d.name = :name')
            //->andWhere('st = :name')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('ss')
            )) // les skills qui n'ont pas de studentSkills correspondants à cet élève
            ->setParameter('name', $discipline);
        //->setParameter('student', $student);
        //->andWhere('ss.student = :student')
    }

    public function findDisciplineLevels($discipline)
    {
        return $this->createQueryBuilder('dl')
            ->join('dl.discipline', 'd')
            ->join('dl.level', 'l')
            ->join('dl.skills', 's')
            ->andWhere('d.name = :name')
            ->addSelect('dl', 'd', 's', 'l')
            ->setParameter('name', $discipline)
            ->getQuery()
            ->getResult();
    }

}
