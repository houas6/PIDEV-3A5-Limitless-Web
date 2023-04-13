<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 *
 * @method Produit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Produit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Produit[]    findAll()
 * @method Produit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    public function save(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Produit $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Produit[] Returns an array of Produit objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Produit
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function SortBynomProduit(){
    return $this->createQueryBuilder('e')
        ->orderBy('e.nomproduit','ASC')
        ->getQuery()
        ->getResult()
        ;
}

public function SortBydescriptionProduit()
{
    return $this->createQueryBuilder('e')
        ->orderBy('e.description','ASC')
        ->getQuery()
        ->getResult()
        ;
}
public function SortBycategorieProduit()
{
    
    return $this->createQueryBuilder('p')
        ->join('p.idcategorie', 'c')
        ->orderBy('c.nomcategorie', 'ASC')
        ->getQuery()
        ->getResult();
}
public function findBydescriptionProduit( $description)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.description LIKE :description')
        ->setParameter('description','%' .$description. '%')
        ->getQuery()
        ->execute();
}
public function findBynomProduit( $nomproduit)
{
    return $this-> createQueryBuilder('e')
        ->andWhere('e.nomproduit LIKE :nomproduit')
        ->setParameter('nomproduit','%' .$nomproduit. '%')
        ->getQuery()
        ->execute();
}
public function findBycategorieProduit( $categorie)
{
    return $this->createQueryBuilder('p')
        ->join('p.idcategorie', 'c')
        ->andWhere('c.nomcategorie LIKE :categorie')
        ->setParameter('categorie', '%' . $categorie . '%')
        ->getQuery()
        ->getResult();
}

}
