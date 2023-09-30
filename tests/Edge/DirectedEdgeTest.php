<?php

namespace Edge;

use PHPUnit\Framework\TestCase;
use GraphPHP\Edge\DirectedEdge;
use GraphPHP\Node\Node;

class DirectedEdgeTest extends TestCase
{
    public function testDirectedEdgeCreation()
    {
        $source = new Node('A');
        $target = new Node('B');
        $edge = new DirectedEdge($source, $target);

        $this->assertInstanceOf(DirectedEdge::class, $edge);
    }

    public function testGetSource()
    {
        $source = new Node('A');
        $target = new Node('B');
        $edge = new DirectedEdge($source, $target);

        $this->assertSame($source, $edge->getSource());
    }

    public function testGetTarget()
    {
        $source = new Node('A');
        $target = new Node('B');
        $edge = new DirectedEdge($source, $target);

        $this->assertSame($target, $edge->getTarget());
    }
}