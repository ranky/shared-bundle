<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Domain;


use Ranky\SharedBundle\Domain\ValueObject\Collection;

/**
 * @extends Collection<Page>
 */
class PageCollection extends Collection
{

    protected function type(): string
    {
        return Page::class;
    }

    /**
     * @return array<int|string, array<string, mixed>>
     */
    public function toArray(): array
    {
        $array = [];
        foreach ($this->getIterator() as $key => $item) {
            /* @var $item Page */
            $array[$key] = [
                'id' => $item->getId(),
                'title' => $item->getTitle(),
                'description' => $item->getDescription(),
            ];
        }

        return $array;
    }

    /**
     * @param array<string, mixed> $data
     * @return Collection<Page>
     */
    public static function fromArray(array $data): Collection
    {
        $items = [];
        foreach ($data as $item) {
            $page = new Page();
            $page->setId($item['id']);
            $page->setTitle($item['title']);
            $page->setDescription($item['description']);
            $items[] = $page;
        }

        return new self($items);
    }
}
