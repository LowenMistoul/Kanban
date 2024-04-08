<?php

namespace App\Repository;

use App\Entity\Tableau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tableau>
 *
 * @method Tableau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tableau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tableau[]    findAll()
 * @method Tableau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tableau::class);
    }

//    /**
//     * @return Tableau[] Returns an array of Tableau objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findOneById($value): ?Tableau
   {
       return $this->createQueryBuilder('t')
           ->andWhere('t.id = :val')
           ->setParameter('val', $value)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

    public function findAllByUserID($value): array
    {
        return $this->createQueryBuilder('tableau')
            ->leftJoin('tableau.Owner', 'tableau_user')
            ->where('tableau_user = :userId')
            ->setParameter('userId', $value)
            ->getQuery()
            ->getResult();
        ;
    }

    public function findAllColonnesByTableauID($value): array
    {
        return $this->createQueryBuilder('tableau')
            ->leftJoin('tableau.colonnes', 'colonnes')
            ->where('colonnes.tableau_id = :tableauId')
            ->setParameter('tableauId', $value)
            ->getQuery()
            ->getResult();
        ;
    }

    public function findAllTicketsByTableauID($value): array
    {
        return $this->createQueryBuilder('tableau')
            ->leftJoin('tableau.tickets', 'tickets')
            ->where('tickets.tableau_id = :tableauId')
            ->setParameter('tableauId', $value)
            ->getQuery()
            ->getResult();
        ;
    }
}
