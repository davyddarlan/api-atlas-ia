<?php

namespace App\Repository;

use App\Entity\Marcador;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Marcador|null find($id, $lockMode = null, $lockVersion = null)
 * @method Marcador|null findOneBy(array $criteria, array $orderBy = null)
 * @method Marcador[]    findAll()
 * @method Marcador[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MarcadorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Marcador::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Marcador $entity, bool $flush = true): void
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
    public function remove(Marcador $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function qtdMarcadoresAssocidos($uuid): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT COUNT(especie_id) AS qtd_marcador FROM especie_marcador 
            WHERE especie_id=(SELECT id FROM especie 
            WHERE uuid=:uuid)
        ';

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['uuid' => $uuid]);

        return $resultSet->fetchAllAssociative()[0]['qtd_marcador'];
    }
}
