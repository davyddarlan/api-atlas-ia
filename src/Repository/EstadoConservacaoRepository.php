<?php

namespace App\Repository;

use App\Entity\EstadoConservacao;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EstadoConservacao|null find($id, $lockMode = null, $lockVersion = null)
 * @method EstadoConservacao|null findOneBy(array $criteria, array $orderBy = null)
 * @method EstadoConservacao[]    findAll()
 * @method EstadoConservacao[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EstadoConservacaoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EstadoConservacao::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(EstadoConservacao $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(EstadoConservacao $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return EstadoConservacao[] Returns an array of EstadoConservacao objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EstadoConservacao
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
