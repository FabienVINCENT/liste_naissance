<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function findAll(): array
    {
        return parent::findBy([], ['id' => 'ASC']);
    }

    public function search(string $q): array
    {
        $query = $this->createQueryBuilder('a');
        if ($q) {
            $query->where('MATCH_AGAINST(a.title, a.description) AGAINST(:q boolean)>0')
                ->setParameter('q', $q);
        }

        return $query->getQuery()->getResult();
    }
}
