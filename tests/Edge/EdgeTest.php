<?php

namespace Edge;

use PHPUnit\Framework\TestCase;
use GraphPHP\Edge\Edge;
use GraphPHP\Node\Node;

class EdgeTest extends TestCase
{
    public function testEdgeCreationWithDefaultValues()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $this->assertEquals('A-B', $edge->getId());
        $this->assertEquals([$nodeA, $nodeB], $edge->getNodes());
        $this->assertEquals(0.0, $edge->getWeight());
    }

    public function testEdgeCreationWithCustomWeight()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB, 5.0);

        $this->assertEquals(5.0, $edge->getWeight());
    }

    public function testEdgeCreationWithCustomId()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB, 5.0, 'custom-id');

        $this->assertEquals('custom-id', $edge->getId());
    }

    public function testSetWeight()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $edge->setWeight(10.0);
        $this->assertEquals(10.0, $edge->getWeight());
    }

    public function testGetNodeA()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $this->assertSame($nodeA, $edge->getNodeA());
    }

    public function testGetNodeB()
    {
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $this->assertSame($nodeB, $edge->getNodeB());
    }
}