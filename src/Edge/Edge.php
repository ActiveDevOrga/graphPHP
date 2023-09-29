<?php

namespace GraphPHP\Edge;

use GraphPHP\Node\Node;

class Edge
{
    private string $id;
    protected Node $nodeA;
    protected Node $nodeB;
    private float $weight;

    public function __construct(Node $nodeA, Node $nodeB, float $weight = 0.0) {
        $this->nodeA = $nodeA;
        $this->nodeB = $nodeB;
        $this->weight = $weight;

        // Set the ID based on the node IDs
        $this->id = $nodeA->getId() . '-' . $nodeB->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getNodes(): array
    {
        return [$this->nodeA, $this->nodeB];
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight($weight): Edge
    {
        $this->weight = $weight;

        return $this;
    }

    public function getNodeA(): Node
    {
        return $this->nodeA;
    }

    public function getNodeB(): Node
    {
        return $this->nodeB;
    }
}