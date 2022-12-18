<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Filter\Visitor;

use Ranky\SharedBundle\Filter\Driver;

/**
 * @implements \IteratorAggregate<string, array<FilterVisitor>>
 */
class VisitorCollection implements \IteratorAggregate
{
    /**
     * @var array<string, array<FilterVisitor>>
     */
    private array $visitors;

    /**
     * @param array<string, array<FilterVisitor>> $visitors
     */
    public function __construct(array $visitors = [])
    {
        $this->visitors = $visitors;
        // add default Visitor for every driver
        foreach (Driver::cases() as $driver){
            if (!\array_key_exists($driver->value, $this->visitors)){
                $this->visitors[$driver->value] = [];
            }
        }
        \array_unshift($this->visitors[Driver::DOCTRINE_ORM->value], new DoctrineExpressionFilterVisitor());
        \array_unshift($this->visitors[Driver::SQL->value], new SqlExpressionFilterVisitor());
    }

    public function addVisitor(string $driver, FilterVisitor $filterVisitor): void
    {
        $this->visitors[$driver][] = $filterVisitor;
    }

    /**
     * @param string $driver
     * @return FilterVisitor[]
     */
    public function getVisitorsByDriver(string $driver): array
    {
        return $this->visitors[$driver];
    }

    /**
     * @return array<string, array<FilterVisitor>>
     */
    public function getVisitors(): array
    {
       return $this->visitors;
    }


    /**
     * @return \ArrayIterator<string, array<FilterVisitor>>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->visitors);
    }
}
