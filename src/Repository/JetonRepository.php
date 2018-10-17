<?php

namespace App\Repository;

use App\Entity\Jeton;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Jeton|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jeton|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jeton[]    findAll()
 * @method Jeton[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JetonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Jeton::class);
    }

    public function findByTypeValue()
    {
        $jetons = $this->createQueryBuilder('j')
            ->orderBy('j.type','ASC')
            ->orderBy('j.valeur', 'ASC')
            ->getQuery()
            ->getResult();

        $tJetons = [];
        $tJetons['Cadillac'] = [];
        $tJetons['Argent'] = [];
        $tJetons['Cigarette'] = [];
        $tJetons['Alcool'] = [];
        $tJetons['Oeuvre'] = [];
        $tJetons['Arme'] = [];
        $tJetons['Drogue'] = [];
        $tJetons['Chap_3'] = [];
        $tJetons['Chap_4'] = [];
        $tJetons['Chap_5'] = [];

        foreach($jetons as $jeton) {
            $tJetons[$jeton->getType()->getNom()][] = $jeton->getId();
        }

        shuffle($tJetons['Chap_3']);
        shuffle($tJetons['Chap_4']);
        shuffle($tJetons['Chap_5']);

        return $tJetons;
    }

    public function findByArrayId()
    {
        return $this->createQueryBuilder('jj')
            ->from($this->getClassName(), 'j', 'jj.id')
            ->orderBy('j.id', 'ASC')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

    }

//    /**
//     * @return Jeton[] Returns an array of Jeton objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('j.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Jeton
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
