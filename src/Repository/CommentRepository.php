<?php

namespace App\Repository;

use App\Entity\AcademicYear;
use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function findCommentByStudent($id)
    {
        $currentYear = $this->getEntityManager()
            ->getRepository(AcademicYear::class)
            ->createQueryBuilder('y')
            ->where(':date BETWEEN y.startDate AND y.endDate')
            ->setParameter('date', date('Y-m-d'))
            ->getQuery()
            ->getSingleResult();

        $startDate = $currentYear->getStartDate();
        $endDate = $currentYear->getEndDate();

        $qb = $this->createQueryBuilder('c')
            ->innerJoin('c.teacher', 't')
            ->innerJoin('c.discipline', 'd')
            ->innerJoin('d.category', 'ca')
            ->addSelect('t', 'd', 'ca')
            ->innerJoin('c.student', 's')
            ->where('s.id = :id')
            ->andWhere('c.createdAt BETWEEN :startDate AND :endDate')
            ->setParameter('id', $id)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->distinct()
            ->orderBy('c.id', 'DESC')
            ->getQuery();

        return $qb->getArrayResult();
    }
}
