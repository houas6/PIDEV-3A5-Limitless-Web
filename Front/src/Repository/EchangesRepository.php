<?php

namespace App\Repository;

use App\Entity\Echanges;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Twilio\Rest\Client;

/**
 * @extends ServiceEntityRepository<Echanges>
 *
 * @method Echanges|null find($id, $lockMode = null, $lockVersion = null)
 * @method Echanges|null findOneBy(array $criteria, array $orderBy = null)
 * @method Echanges[]    findAll()
 * @method Echanges[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EchangesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Echanges::class);
    }

    public function save(Echanges $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Echanges $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Echanges[] Returns an array of Echanges objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Echanges
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

public function sms(){
    // Your Account SID and Auth Token from twilio.com/console
            $sid = 'ACbb7646fae7982453e89663d5007702ff';
            $auth_token = '42fd05bf0e7cc15bd323ee86192e29a2';
    // In production, these should be environment variables. E.g.:
    // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]
    // A Twilio number you own with SMS capabilities
            $twilio_number = "+16812069508";
    
            $client = new Client($sid, $auth_token);
            $client->messages->create(
            // the number you'd like to send the message to
                '+21658867380',
                [
                    // A Twilio phone number you purchased at twilio.com/console
                    'from' => '+16812069508',
                    // the body of the text message you'd like to send
                    'body' => 'Un echange a été ajoutée'
                ]
            );
        }

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
