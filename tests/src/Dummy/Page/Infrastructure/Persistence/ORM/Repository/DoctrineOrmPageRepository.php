<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Infrastructure\Persistence\ORM\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 */
class DoctrineOrmPageRepository extends ServiceEntityRepository implements PageRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    /**
     * @param int $id
     * @return Page
     */
    public function getById(int $id): Page
    {
        /** @var Page|null $page */
        $page = $this->find($id);
        if (!$page) {
            throw new \RuntimeException('Page not found');
        }
        return $page;
    }

    /**
     * @return array<int,Page>
     */
    public function getAll(): array
    {
        /** @var array<int,Page> $pages */
        $pages = $this->findAll();
        return $pages;
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
