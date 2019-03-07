<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }


    /**
     * EXO 1 : Récupérer la liste les films par ordre alphabétique
     * Méthode en DQL (Doctrine Query Language)
     * 
     *  @return Movie[] Returns an array of Movie objects
     */
    public function findAllDQLOrderedByName()
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT m 
                FROM App\Entity\Movie m 
                ORDER BY m.title ASC
            ')
            ->getResult();
    }

    /**
     * EXO 1 : Récupérer la liste les films par ordre alphabétique
     * Autre méthode avec Doctrine Query Builder
     * 
     *  @return Movie[] Returns an array of Movie objects
     */
    public function findAllQueryBuilderOrderedByName()
    {
        $query = $this->createQueryBuilder('m')
                      ->orderBy('m.title', 'ASC'); // Ou ->add('orderBy', 'a.title ASC')

        return $query->getQuery()->getResult();
    }

    // retourne les derniers films avec nombre limité de résultats
    public function lastRelease($limit){

        $query = $this->createQueryBuilder('m')
                      ->orderBy('m.id', 'DESC')
                      ->setMaxResults( $limit );

        return $query->getQuery()->getResult();
    }

    // retourne la liste des films filtré par titre
    public function findByTitle($title){

        $query = $this->createQueryBuilder('m')
                      ->where('m.title LIKE :searchTitle')
                      ->setParameter('searchTitle', '%' . $title . '%')
                      ->orderBy('m.title', 'ASC'); 

        return $query->getQuery()->getResult();
    }

}
