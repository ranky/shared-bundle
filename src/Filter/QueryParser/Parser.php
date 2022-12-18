<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\QueryParser;

use Ranky\SharedBundle\Filter\QueryParser\AST\Node\Node;
use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitorNode;
use Ranky\SharedBundle\Filter\QueryParser\Exception\ParserException;
use Ranky\SharedBundle\Filter\QueryParser\Output\OutputVisitor;

class Parser
{
    private ?Lexer $lexer = null;
    private ?AST $astParser = null;
    private ?OutputVisitor $outputVisitor = null;
    /**
     * @var array<VisitorNode>
     */
    private array $visitorNodes = [];

    public function __construct()
    {
    }

    public function parse(string $input): self
    {
        $this->lexer = new Lexer($input);
        $this->lexer->tokenize();

        return $this;
    }

    public function addVisitorNode(VisitorNode $visitorNode): void
    {
        $this->visitorNodes[] = $visitorNode;
    }

    public function setOutputVisitor(OutputVisitor $outputVisitor): void
    {
        $this->visitorNodes[] = $outputVisitor;
        $this->outputVisitor  = $outputVisitor;
    }

    /**
     * @return TokenStream<Token>
     */
    public function getTokenStream(): TokenStream
    {
        return $this->lexer->getTokenStream();
    }

    /**
     * @return \ArrayIterator<int,Node>
     */
    public function getAST(): \ArrayIterator
    {
        if (!$this->lexer) {
            throw new ParserException(
                'Lexer did not define it. Make sure you call the "parse" method with the input parameter'
            );
        }
        $this->astParser = new AST($this->getTokenStream());
        foreach ($this->visitorNodes as $visitorNode) {
            if (!$this->outputVisitor && $visitorNode instanceof OutputVisitor) {
                $this->outputVisitor = $visitorNode;
            }
            $this->astParser->addVisitor($visitorNode);
        }

        return $this->astParser->getAST();
    }


    /**
     * @return mixed
     */
    public function getOutput(): mixed
    {
        if (!$this->outputVisitor) {
            throw new ParserException('Output visitor is not defined when invoking getOutput()');
        }
        if (!$this->astParser) {
            $this->getAST();
        }

        return $this->outputVisitor->getOutput();
    }


    /**
     * @return array<int|string,mixed>
     */
    public function toArray(): array
    {
        if (!$this->astParser) {
            $this->getAST();
        }

        return $this->astParser->toArray();
    }

}
