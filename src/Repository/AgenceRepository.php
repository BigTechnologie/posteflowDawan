<?php

namespace App\Repository;

use App\Entity\Agence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AgenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $r)
    {
        parent::__construct($r, Agence::class);
    }

    /**
     * Recherche les agences par nom, ville ou code postal
     *
     * @param string|null $q Recherche libre (nom, ville ou code postal)
     * @return array
     */
    public function rechercher(?string $q = null): array 
    {
        $qb = $this->createQueryBuilder('a') 
        ->orderBy('a.ville', 'ASC'); 

        if ($q) {
            $qb->andWhere('a.nom LIKE :q OR a.ville LIKE :q OR a.codePostal LIKE :q') 
                ->setParameter('q', '%' . $q . '%'); 
        }

        return $qb->getQuery() 
            ->getResult(); 
    }
}
