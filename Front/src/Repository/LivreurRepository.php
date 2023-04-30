<?php

namespace App\Repository;

use App\Entity\Livreur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livreur>
 *
 * @method Livreur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livreur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livreur[]    findAll()
 * @method Livreur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreur::class);
    }

    public function save(Livreur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Livreur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Livreur[] Returns an array of Livreur objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livreur
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
public function SortBynom(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.nom','ASC')
        ->getQuery()
        ->getResult()
        ;
}
public function SortBymail(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.mail','ASC')
        ->getQuery()
        ->getResult()
        ;
}
public function SortBytelephone(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.telephone','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function findBynom( $nom)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.nom LIKE :nom')
        ->setParameter('nom','%' .$nom. '%')
        ->getQuery()
        ->execute();
}
public function findBymail( $mail)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.mail LIKE :mail')
        ->setParameter('mail','%' .$mail. '%')
        ->getQuery()
        ->execute();
}

public function findBytelephone( $telephone)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.telephone LIKE :telephone')
        ->setParameter('telephone','%' .$telephone. '%')
        ->getQuery()
        ->execute();
}

}
