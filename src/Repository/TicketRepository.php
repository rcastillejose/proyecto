<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\Estado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function findByOpen(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            "SELECT t
            FROM App\Entity\Ticket t 
            WHERE t.estado != 2
            ORDER BY t.fecha ASC");
           //AND t.grupo = :id
        // )->setParameter('id', $id);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function findByGroup($grupo): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT t
            FROM App\Entity\Ticket t 
            WHERE t.estado != 2
           AND t.grupo = :grupo
           ORDER BY t.fecha ASC'
         )->setParameter('grupo', $grupo);

        // returns an array of Product objects
        return $query->getResult();
    }

    public function cerrarTickets($cliente): int
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'UPDATE App\Entity\Ticket t SET t.estado = 2 WHERE t.cliente = :cliente'
         )->setParameter('cliente', $cliente);

        // returns an array of Product objects
        return $query->execute();
    }


    // /**
    //  * @return Ticket[] Returns an array of Ticket objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
