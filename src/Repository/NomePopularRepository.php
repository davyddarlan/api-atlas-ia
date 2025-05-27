<?php

namespace App\Repository;

use App\Entity\NomePopular;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NomePopular|null find($id, $lockMode = null, $lockVersion = null)
 * @method NomePopular|null findOneBy(array $criteria, array $orderBy = null)
 * @method NomePopular[]    findAll()
 * @method NomePopular[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NomePopularRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NomePopular::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(NomePopular $entity, bool $flush = true): void
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
    public function remove(NomePopular $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function qtdNomesPopularesAssocidos($uuid): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT COUNT(especie_id) AS qtd_nome_popular FROM especie_nome_popular 
            WHERE especie_id=(SELECT id FROM especie 
            WHERE uuid=:uuid)
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uuid' => $uuid]);

        return $resultSet->fetchAllAssociative()[0]['qtd_nome_popular'];
    }
}
