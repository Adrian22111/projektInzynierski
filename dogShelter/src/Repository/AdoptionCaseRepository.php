<?php

namespace App\Repository;

use App\Entity\AdoptionCase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\AST\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdoptionCase>
 *
 * @method AdoptionCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdoptionCase|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdoptionCase[]    findAll()
 * @method AdoptionCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdoptionCaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdoptionCase::class);
    }

    public function save(AdoptionCase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(AdoptionCase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // public function findEmployeeCases($employeeId): array
    // {
    //    return $this->createQueryBuilder('a')
    //        ->innerJoin('a.employee', 'e')
    //        ->addSelect('e')
    //        ->innerJoin('a.documents','d')
    //        ->addSelect('d')
    //        ->andWhere('e.id = :id')
    //        ->setParameter('id',$employeeId)
    //        ->andWhere('a.archived = :archived')
    //        ->andWhere('e.archived = :archived')
    //        ->andWhere('d.archived = :archived')
    //        ->setParameter('archived',false)
    //        ->orderBy('a.id', 'ASC')
    //        ->getQuery()
    //        ->getResult()
    //     ;
    // }
    public function findEmployeeCases($employeeId): array
    {
       return $this->createQueryBuilder('a')
           ->addSelect('a')
           ->join('a.employee','e')
           ->andWhere('a.archived = :archived')
           ->setParameter('archived',false)
           ->andWhere('e.id = :employeeId')
           ->setParameter('employeeId',$employeeId)
           ->orderBy('a.id', 'ASC')
           ->getQuery()
           ->getResult()
        ;
    }

    public function findClientCases($clientId): array
    {
       return $this->createQueryBuilder('a')
           ->addSelect('a')
           ->join('a.client','c')
           ->andWhere('a.archived = :archived')
           ->setParameter('archived',false)
           ->andWhere('c.id = :clientId')
           ->setParameter('clientId',$clientId)
           ->orderBy('a.id', 'ASC')
           ->getQuery()
           ->getResult()
        ;
    }




//    /**
//     * @return AdoptionCase[] Returns an array of AdoptionCase objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?AdoptionCase
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
