<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageRepository;
use Doctrine\Persistence\ManagerRegistry;

class DoctrineOrmPageRepository extends ServiceEntityRepository implements PageRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function getById(int $id): Page
    {
        return $this->find($id);
    }

    public function getAll(): array
    {
        return $this->findAll();
    }

    public function save(Page $page): void
    {
        $this->getEntityManager()->persist($page);
        $this->getEntityManager()->flush();
    }

    public function delete(Page $page): void
    {
       $this->getEntityManager()->remove($page);
       $this->getEntityManager()->flush();
    }
}
