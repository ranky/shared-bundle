<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Filter\QueryParser;

use Ranky\SharedBundle\Filter\QueryParser\Output\DoctrineORMOutputVisitor;
use Ranky\SharedBundle\Filter\QueryParser\Output\SQLOutputVisitor;
use Ranky\SharedBundle\Filter\QueryParser\Parser;
use Ranky\SharedBundle\Tests\BaseIntegrationTestCase;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\Page;
use Ranky\SharedBundle\Tests\Dummy\Page\Domain\PageCriteria;

class ParserTest extends BaseIntegrationTestCase
{

    /**
     * @dataProvider dataProviderOutputVisitor
     */
    public function testItShouldParserMultiplesQueryWithCustomOutputVisitor(
        string $input,
        string $type,
        string $expected
    ): void {
        $pageCriteria = PageCriteria::default();
        $queryParser  = new Parser();
        $queryParser->parse($input);

        if ($type === 'doctrine') {
            $queryBuilder          = self::getDoctrineManager()
                ->createQueryBuilder()
                ->select('p.id, p.title')
                ->from(Page::class, 'p');
            $doctrineOutputVisitor = new DoctrineORMOutputVisitor($pageCriteria, $queryBuilder);
            $queryParser->setOutputVisitor($doctrineOutputVisitor);
            $query = $queryParser->getOutput()->getQuery();
            $this->assertSame($expected, $query->getSQL());
            $this->assertNotNull($query->getResult());
        } else {
            $sqlOutputVisitor = new SQLOutputVisitor($pageCriteria);
            $queryParser->setOutputVisitor($sqlOutputVisitor);
            $this->assertSame($expected, $queryParser->getOutput());
        }
    }

    /**
     * @return array<array<int, string>>
     */
    public function dataProviderOutputVisitor(): array
    {
        return [
            [
                "eq('title','foo') or like('title','bar')",
                'sql',
                'p.title = "foo" or p.title like "%bar%"',
            ],
            [
                "eq('title','foo') or like('title','bar')",
                'doctrine',
                'SELECT p0_.id AS id_0, p0_.title AS title_1 FROM page p0_ WHERE p0_.title = ? OR p0_.title LIKE ?',
            ],
            [
                "(eq(id,317) and eq('title','bar') or eq('title','baz')) or like('title','baz@gmail.com')",
                'sql',
                '(p.id = 317 and p.title = "bar" or p.title = "baz") or p.title like "%baz@gmail.com%"',
            ],
            [
                "(eq(id,317) and eq('title','bar') or eq('title','baz')) or like('title','baz@gmail.com')",
                'doctrine',
                'SELECT p0_.id AS id_0, p0_.title AS title_1 FROM page p0_ WHERE (p0_.id = ? AND p0_.title = ? OR p0_.title = ?) OR p0_.title LIKE ?',
            ],
        ];
    }
}
