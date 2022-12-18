<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Tests\Dummy\Page\Domain;

use Ranky\SharedBundle\Filter\Criteria;

class PageCriteria extends Criteria
{
    public const MODEL_ALIAS              = 'p';
    public const DEFAULT_PAGINATION_LIMIT = 30;
    public const DEFAULT_PAGINATION_RANGE = 2;
    public const DEFAULT_ORDER_FIELD      = 'createdAt';
    public const DEFAULT_ORDER_DIRECTION  = 'DESC';

    public static function normalizeNameFields(): array
    {
        return [
            'id' => 'p.id',
            'title' => 'p.title',
            'description' => 'p.description',
            'createdAt' => 'p.createdAt',
            'updatedAt' => 'p.updatedAt',
        ];
    }

    public static function normalizeValues(): array
    {
        return [];
    }

    public static function modelClass(): string
    {
        return Page::class;
    }

    public static function modelAlias(): string
    {
        return self::MODEL_ALIAS;
    }
}
