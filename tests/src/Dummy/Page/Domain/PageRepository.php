<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Dummy\Page\Domain;


interface PageRepository
{
    public function getById(int $id): Page;

    /**
     * @return array<Page>
     */
    public function getAll(): array;

    public function save(Page $page): void;

    public function delete(Page $page): void;

}
