<?php

declare(strict_types=1);


namespace Ranky\SharedBundle\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use Ranky\SharedBundle\Domain\Exception\InvalidHandlerException;
use Ranky\SharedBundle\Domain\Service\ValidateHandlersTrait;

interface FileCompress
{
}

class Test1Handler implements FileCompress
{

}

class Test2Handler implements FileCompress
{

}

class Test3Handler
{
}
class ValidateHandlersTraitTest extends TestCase
{

    use ValidateHandlersTrait;

    public function testValidateHandlersWithArray(): void
    {
        $handlers = [
            new Test1Handler(),
            new Test2Handler(),
        ];

        $this->assertSame(
            $handlers,
            $this->validateHandlers($handlers, FileCompress::class)
        );
    }

    public function testValidateHandlersWithArrayIterator(): void
    {
        $array = [
            new Test1Handler(),
            new Test2Handler(),
        ];
        $handlers = new \ArrayIterator($array);


        $this->assertEquals(
            $array,
            $this->validateHandlers($handlers, FileCompress::class)
        );
    }

    public function testValidateHandlersWithArrayThrowException(): void
    {
        $handlers = [
            new Test1Handler(),
            new Test2Handler(),
            new Test3Handler(),
        ];

        $this->expectException(InvalidHandlerException::class);
        $this->validateHandlers($handlers, FileCompress::class);
    }

}
