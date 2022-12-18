<?php

declare(strict_types=1);

namespace Ranky\MediaBundle\Tests\Domain;

use Faker\Factory;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;

final class PageFactory
{

    public static function create(int $id, string $title, string $description): Page
    {
        $page = new Page();
        $page->setId($id);
        $page->setTitle($title);
        $page->setDescription($description);

        return $page;
    }

    /**
     * @return array<Page>
     */
    public static function random(?int $numbers = 1): array
    {
        $pages = [];
        $faker = Factory::create();

        for ($i = 1; $i <= $numbers; $i++) {
            $page = new Page();
            $page->setId($i);
            $page->setTitle($faker->paragraph(1));
            $page->setDescription($faker->realText());
            $pages[] = $page;
        }

        return $pages;
    }

}
