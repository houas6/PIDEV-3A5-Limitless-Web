<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;

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
public function sms(){
    // Your Account SID and Auth Token from twilio.com/console
            $sid = 'AC28d52cd023492602f11de9a19077ce47';
            $auth_token = '025ed7bac41e4972fa59cd991a1763f1';
    // In production, these should be environment variables. E.g.:
    // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
    // A Twilio number you own with SMS capabilities
            $twilio_number = "+16206788051";
    
            $client = new Client($sid, $auth_token);
            $client->messages->create(
            // the number you'd like to send the message to
                '+21651092218',
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => '+16206788051',
                    // the body of the text message you'd like to send
                    'body' => 'Un produit a été ajoutée'
                ]
            );
        }
}
