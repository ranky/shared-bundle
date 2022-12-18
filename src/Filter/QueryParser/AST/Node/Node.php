<?php

declare(strict_types=1);

namespace Ranky\SharedBundle\Filter\QueryParser\AST\Node;

use Ranky\SharedBundle\Filter\QueryParser\AST\Visitor\VisitableNode;
use Ranky\SharedBundle\Filter\QueryParser\Exception\NodeException;
use Ranky\SharedBundle\Filter\QueryParser\Token;

/**
 * @implements \IteratorAggregate<int, Node>
 */
abstract class Node implements VisitableNode, \Countable, \IteratorAggregate
{
    private Token $token;
    /** @var array<Node> */
    protected array $nodes;
    /** @var array<string, mixed> */
    protected array $attributes;

    /**
     * @param Token $token
     * @param array<Node> $nodes
     * @param array<string,mixed> $attributes
     */
    public function __construct(Token $token, array $nodes = [], array $attributes = [])
    {
        $this->nodes      = $nodes;
        $this->attributes = $attributes;
        $this->token      = $token;
    }

    abstract public function getType(): string;
    /**
     * @return array<Node>
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @param array<Node> $nodes
     */
    public function setNodes(array $nodes): void
    {
        $this->nodes = $nodes;
    }

    public function addNode(self $node): void
    {
        $this->nodes[] = $node;
    }


    public function getToken(): Token
    {
        return $this->token;
    }


    public function setToken(Token $token): void
    {
        $this->token = $token;
    }


    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function addAttribute(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function hasAttribute(string $key): bool
    {
        return \array_key_exists($key, $this->attributes);
    }

    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    public function setAttribute(string $key, mixed $value): void
    {
        if (!\array_key_exists($key, $this->attributes)) {
            throw new NodeException(\sprintf('The attribute %s does not exist.', $key));
        }
        $this->attributes[$key] = $value;
    }

    /**
     * @param array<string,string> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }


    /**
     * @return \Traversable<int, Node>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->nodes);
    }

    public function count(): int
    {
        return \count($this->nodes);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $attributes = [];
        foreach ($this->attributes as $name => $value) {
            $attributes[$name] = $value instanceof \UnitEnum ? $value->name : $value;
        }

        return [
            'type' => $this->getType(),
            'token' => $this->token->toArray(),
            'attributes' => $attributes,
            'nodes' => \array_map(static fn($node) => $node->toArray(), $this->nodes),
        ];
    }


}
