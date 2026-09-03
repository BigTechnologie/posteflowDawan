<?php

namespace App\Repository;

use App\DTO\ColisSearchDTO;
use App\Entity\Colis;
use App\Enum\StatutColis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ColisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Colis::class);
    }

    /**
     * Recherche des colis à partir d’un DTO contenant
     * les différents critères facultatifs.
     *
     * @return list<Colis>
     */
    public function rechercher(
        ColisSearchDTO $search
    ): array {
        $queryBuilder = $this->createQueryBuilder('colis') 
            ->leftJoin('colis.client', 'client') 
            ->addSelect('client') 
            ->leftJoin('colis.agenceDepot', 'agence') 
            ->addSelect('agence') 
            ->orderBy('colis.createdAt', 'DESC'); 

        /*
         * Recherche textuelle générale.
         *
         * Le terme est recherché dans :
         *
         * - le numéro de suivi ;
         * - le nom du destinataire ;
         * - le nom du client.
         */
        if ($search->terme !== null) {
            $queryBuilder 
                ->andWhere( 
                    $queryBuilder->expr()->orX( // On ajoute une condition OR
                        'colis.numeroSuivi LIKE :terme', 
                        'colis.destinataire LIKE :terme', 
                        'client.nom LIKE :terme' 
                    )
                )
                ->setParameter( // Permet de binder le paramètre 'terme' avec la valeur recherchée
                    'terme', // terme
                    '%'.$search->terme.'%' // valeur recherchée
                );
        }

        /*
         * Le DTO contient directement un objet StatutColis.
         */
        if ($search->statut !== null) {
            $queryBuilder
                ->andWhere('colis.statut = :statut') 
                ->setParameter( 
                    'statut', 
                    $search->statut 
                );
        }

        /*
         * Recherche partielle sur la ville de livraison.
         */
        if ($search->ville !== null) {
            $queryBuilder
                ->andWhere(
                    'colis.villeLivraison LIKE :ville' 
                )
                ->setParameter(
                    'ville', // ville
                    '%'.$search->ville.'%' 
                );
        }

        return $queryBuilder
            ->getQuery() // On récupère la requête
            ->getResult(); // On exécute la requête
    }

    /**
     * 
     * @return list<array{
     *     statut: string,
     *     total: string|int
     * }>
     */
    public function compterParStatut(): array
    {
        return $this->createQueryBuilder('c')
            ->select(
                'c.statut AS statut',
                'COUNT(c.id) AS total'
            )
            ->groupBy('c.statut')
            ->orderBy('total', 'DESC')
            ->getQuery()
            ->getArrayResult(); // type: array[]
    }

    /**
     *
     * @return list<Colis>
     */
    public function derniersColis(int $limit = 8): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->addSelect('client')
            ->leftJoin('c.agenceDepot', 'agence')
            ->addSelect('agence')
            ->orderBy('c.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return list<Colis>
     */
    public function colisEnIncidentDepuis(int $jours): array
    {
        $jours = max(0, $jours);

        $dateLimite = new \DateTimeImmutable(
            sprintf('-%d days', $jours)
        );

        return $this->createQueryBuilder('c')
            ->leftJoin('c.client', 'client')
            ->addSelect('client')
            ->leftJoin('c.agenceDepot', 'agence')
            ->addSelect('agence')
            ->andWhere('c.statut = :statut')
            ->andWhere('c.createdAt <= :dateLimite')
            ->setParameter('statut', StatutColis::INCIDENT)
            ->setParameter('dateLimite', $dateLimite)
            ->orderBy('c.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     *
     * @return array{
     *     parStatut: array,
     *     incidents: list<Colis>,
     *     derniers: list<Colis>
     * }
     */
    public function rechercherPourTableauDeBord(): array
    {
        return [
            'parStatut' => $this->compterParStatut(),
            'incidents' => $this->colisEnIncidentDepuis(1),
            'derniers' => $this->derniersColis(),
        ];
    }
}