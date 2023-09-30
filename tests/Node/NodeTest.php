<?php

namespace Node;

use PHPUnit\Framework\TestCase;
use GraphPHP\Node\Node;

class NodeTest extends TestCase
{
    public function testNodeCreation()
    {
        $node = new Node('A', 'Some Data');

        $this->assertInstanceOf(Node::class, $node);
    }

    public function testGetId()
    {
        $node = new Node('A', 'Some Data');

        $this->assertSame('A', $node->getId());
    }

    public function testGetData()
    {
        $node = new Node('A', 'Some Data');

        $this->assertSame('Some Data', $node->getData());
    }

    public function testSetData()
    {
        $node = new Node('A');
        $node->setData('Updated Data');

        $this->assertSame('Updated Data', $node->getData());
    }

    public function testSetDataReturnsNodeInstance()
    {
        $node = new Node('A');
        $returnedNode = $node->setData('Some Data');

        $this->assertSame($node, $returnedNode);
    }
}