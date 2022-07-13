<?php

namespace App\Repository;

use App\Entity\Descobridor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Descobridor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Descobridor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Descobridor[]    findAll()
 * @method Descobridor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescobridorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Descobridor::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Descobridor $entity, bool $flush = true): void
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
    public function remove(Descobridor $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function qtdDescobridoresAssocidos($uuid): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT COUNT(especie_id) AS qtd_descobridor FROM especie_descobridor 
            WHERE especie_id=(SELECT id FROM especie 
            WHERE uuid=:uuid)
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uuid' => $uuid]);

        return $resultSet->fetchAllAssociative()[0]['qtd_descobridor'];
    }
}
