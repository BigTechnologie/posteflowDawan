<?php
namespace App\Repository;
use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $r)
    {
        parent::__construct($r, Client::class);
    }
    public function rechercher(?string $q = null): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.agenceReference', 'a')
            ->addSelect('a')
            ->orderBy('c.nom', 'ASC');
        if ($q) {
            $qb->andWhere('c.nom LIKE :q OR c.email LIKE :q OR c.ville LIKE :q')
            ->setParameter('q', '%' . $q . '%');
        }
        return $qb->getQuery()->getResult();
    }
    public function compterParVille(): array
    {
        return $this->createQueryBuilder('c')
            ->select('c.ville AS ville, COUNT(c.id) AS total')
            ->groupBy('c.ville')
            ->orderBy('total', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult();
    }
}
