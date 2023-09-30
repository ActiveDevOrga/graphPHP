<?php

namespace Graph;

use GraphPHP\Edge\Edge;
use GraphPHP\Graph\Graph;
use GraphPHP\Node\Node;
use PHPUnit\Framework\TestCase;


class GraphTest extends TestCase
{
    public function testAddNode()
    {
        $graph = new Graph();
        $node = new Node('1');

        $graph->addNode($node);

        $this->assertCount(1, $graph->getNodes());
        $this->assertSame($node, $graph->getNodes()['1']);
    }

    public function testAddNodeTwiceThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("A node with the ID '1' already exists.");

        $graph = new Graph();
        $node = new Node('1');
        $graph->addNode($node);
        $graph->addNode($node);
    }

    public function testRemoveNode()
    {
        $graph = new Graph();
        $node = new Node('1');
        $graph->addNode($node);

        $graph->removeNode($node);

        $this->assertCount(0, $graph->getNodes());
    }

    public function testRemoveNodeWithEdges()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC);

        $graph->addEdge(new Edge($nodeA, $nodeB)); // A -> B
        $graph->addEdge(new Edge($nodeB, $nodeC)); // B -> C

        // There should be 3 nodes and 2 edges before removal
        $this->assertCount(3, $graph->getNodes());
        $this->assertCount(2, $graph->getEdges());

        // Remove node B
        $graph->removeNode($nodeB);

        // After removal, there should be 2 nodes and 0 edges (since both edges were connected to node B)
        $this->assertCount(2, $graph->getNodes());
        $this->assertCount(0, $graph->getEdges());
    }

    public function testRemoveNodeById()
    {
        $graph = new Graph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');

        $graph->addNode($nodeA)->addNode($nodeB);

        // Remove nodeA by its ID
        $graph->removeNodeById($nodeA->getId());

        $this->assertCount(1, $graph->getNodes());
        $this->assertNull($graph->getNodeById($nodeA->getId()));
    }

    public function testGetNodeById()
    {
        $graph = new Graph();
        $nodeA = new Node('A');

        $graph->addNode($nodeA);

        // Retrieve nodeA by its ID
        $retrievedNode = $graph->getNodeById($nodeA->getId());

        $this->assertSame($nodeA, $retrievedNode);
    }

    public function testGetNodeByIdReturnsNullForNonexistentNode()
    {
        $graph = new Graph();

        // Try to retrieve a node that doesn't exist
        $retrievedNode = $graph->getNodeById('nonexistent-id');

        $this->assertNull($retrievedNode);
    }

    public function testAddEdge()
    {
        $graph = new Graph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge);

        $this->assertCount(1, $graph->getEdges());
        $this->assertSame($edge, $graph->getEdges()[$edge->getId()]);
    }

    public function testAddEdgeTwiceThrowsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("An edge with the ID 'A-B' already exists.");

        $graph = new Graph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);
        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge)->addEdge($edge);
    }

    public function testRemoveEdge()
    {
        $graph = new Graph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);
        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge);

        $graph->removeEdge($edge);

        $this->assertCount(0, $graph->getEdges());
    }

    public function testRemoveEdgeById()
    {
        $graph = new Graph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge);

        // Remove the edge by its ID
        $graph->removeEdgeById($edge->getId());

        $this->assertCount(0, $graph->getEdges());
        $this->assertNull($graph->getEdgeById($edge->getId()));
    }

    public function testGetEdgeById()
    {
        $graph = new Graph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new Edge($nodeA, $nodeB);

        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge);

        // Retrieve the edge by its ID
        $retrievedEdge = $graph->getEdgeById($edge->getId());

        $this->assertSame($edge, $retrievedEdge);
    }

    public function testGetEdgeByIdReturnsNullForNonexistentEdge()
    {
        $graph = new Graph();

        // Try to retrieve an edge that doesn't exist
        $retrievedEdge = $graph->getEdgeById('nonexistent-id');

        $this->assertNull($retrievedEdge);
    }

    public function testGetEdge()
    {
        $graph = new \GraphPHP\Graph\Graph();
        $nodeA = new \GraphPHP\Node\Node('A');
        $nodeB = new \GraphPHP\Node\Node('B');
        $edge = new \GraphPHP\Edge\Edge($nodeA, $nodeB, 5);

        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge);

        $retrievedEdge = $graph->getEdge($nodeA, $nodeB);

        $this->assertSame($edge, $retrievedEdge);
    }

    public function testGetEdgeWeight()
    {
        $graph = new \GraphPHP\Graph\Graph();
        $nodeA = new \GraphPHP\Node\Node('A');
        $nodeB = new \GraphPHP\Node\Node('B');
        $edgeWeight = 5.0;
        $edge = new \GraphPHP\Edge\Edge($nodeA, $nodeB, $edgeWeight);

        $graph->addNode($nodeA)->addNode($nodeB)->addEdge($edge);

        $retrievedWeight = $graph->getEdgeWeight($nodeA, $nodeB);

        $this->assertSame($edgeWeight, $retrievedWeight);
    }


    public function testGetEdgeWeightWhenNoEdgeExists()
    {
        $graph = new \GraphPHP\Graph\Graph();
        $nodeA = new \GraphPHP\Node\Node('A');
        $nodeB = new \GraphPHP\Node\Node('B');

        $graph->addNode($nodeA)->addNode($nodeB);

        $retrievedWeight = $graph->getEdgeWeight($nodeA, $nodeB);

        $this->assertSame(INF, $retrievedWeight);
    }

    public function testGetNeighbors()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeA, $nodeC))
            ->addEdge(new Edge($nodeB, $nodeD));

        $neighborsOfA = $graph->getNeighbors($nodeA);
        $this->assertCount(2, $neighborsOfA);
        $this->assertContains($nodeB, $neighborsOfA);
        $this->assertContains($nodeC, $neighborsOfA);
    }

    public function testGetAdjacencyMatrix()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD)
            ->addEdge(new Edge($nodeA, $nodeB, 5.0))
            ->addEdge(new Edge($nodeA, $nodeC, 7.0))
            ->addEdge(new Edge($nodeB, $nodeD, 3.0));

        $matrix = $graph->getAdjacencyMatrix();

        $this->assertSame([
            'A' => ['A' => false, 'B' => 5.0, 'C' => 7.0, 'D' => false],
            'B' => ['A' => 5.0, 'B' => false, 'C' => false, 'D' => 3.0],
            'C' => ['A' => 7.0, 'B' => false, 'C' => false, 'D' => false],
            'D' => ['A' => false, 'B' => 3.0, 'C' => false, 'D' => false],
        ], $matrix);
    }

    public function testShortestPathDijkstra()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD)
            ->addEdge(new Edge($nodeA, $nodeB, 4.0))
            ->addEdge(new Edge($nodeA, $nodeC, 2.0))
            ->addEdge(new Edge($nodeC, $nodeB, 5.0))
            ->addEdge(new Edge($nodeB, $nodeD, 10.0))
            ->addEdge(new Edge($nodeC, $nodeD, 3.0));

        $result = $graph->shortestPathDijkstra($nodeA, $nodeD);

        $this->assertSame(['A', 'C', 'D'], $result['path']);
        $this->assertSame(5.0, $result['cost']);
    }

    public function testShortestPathDijkstraWithNegativeWeight()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Dijkstra's algorithm cannot handle graphs with negative edge weights.");

        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addEdge(new Edge($nodeA, $nodeB, -5.0));

        $graph->shortestPathDijkstra($nodeA, $nodeB);
    }

    public function testShortestPathDijkstraUnconnectedNodes()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');

        $graph->addNode($nodeA)
            ->addNode($nodeB);

        $result = $graph->shortestPathDijkstra($nodeA, $nodeB);

        $this->assertSame([], $result['path']);
        $this->assertSame(INF, $result['cost']);
    }

    public function testShortestPathDijkstraWithZeroWeights()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeA, $nodeC))
            ->addEdge(new Edge($nodeC, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeD))
            ->addEdge(new Edge($nodeC, $nodeD));

        $result = $graph->shortestPathDijkstra($nodeA, $nodeD);

        $this->assertSame(['A', 'B', 'D'], $result['path']);
        $this->assertSame(0.0, $result['cost']);
    }

    public function testShortestPathWithMultiplePathsToSameNode()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD);

        $graph->addEdge(new Edge($nodeA, $nodeB, 10))
            ->addEdge(new Edge($nodeA, $nodeD, 1))
            ->addEdge(new Edge($nodeD, $nodeB, 1))

            ->addEdge(new Edge($nodeB, $nodeC, 1));

        $result = $graph->shortestPathDijkstra($nodeA, $nodeC);
        $this->assertEquals(['A', 'D', 'B', 'C'], $result['path']);
        $this->assertEquals(3, $result['cost']);
    }

    public function testContainsNegativeWeight()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        // Add edges with positive weights
        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD)
            ->addEdge(new Edge($nodeA, $nodeB, 5))
            ->addEdge(new Edge($nodeA, $nodeC, 3))
            ->addEdge(new Edge($nodeC, $nodeB, 2))
            ->addEdge(new Edge($nodeB, $nodeD, 1))
            ->addEdge(new Edge($nodeC, $nodeD, 4));

        $this->assertFalse($graph->containsNegativeWeight());

        // Add an edge with a negative weight
        $graph->addEdge(new Edge($nodeA, $nodeD, -1));

        $this->assertTrue($graph->containsNegativeWeight());
    }

    public function testHasCycle()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        // Add nodes and edges to create a cycle: A -> B -> C -> A
        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeC))
            ->addEdge(new Edge($nodeC, $nodeA));

        $this->assertTrue($graph->hasCycle());

        // Create another graph without a cycle
        $graphNoCycle = new Graph();

        $graphNoCycle->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeC));

        $this->assertFalse($graphNoCycle->hasCycle());

        // Create a graph with a cycle: A -> B -> C -> D -> B
        $graphCycle = new Graph();

        $graphCycle->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addNode($nodeD)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeC))
            ->addEdge(new Edge($nodeC, $nodeD))
            ->addEdge(new Edge($nodeD, $nodeB));

        $this->assertTrue($graphCycle->hasCycle());
    }

    public function testDfsCycleCheck()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        // Add nodes and edges to create a cycle: A -> B -> C -> A
        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeC))
            ->addEdge(new Edge($nodeC, $nodeA));

        $visited = [];
        foreach ($graph->getNodes() as $node) {
            $visited[$node->getId()] = false;
        }

        $this->assertTrue($graph->dfsCycleCheck($nodeA, null, $visited));

        // Reset visited array for another test
        foreach ($graph->getNodes() as $node) {
            $visited[$node->getId()] = false;
        }

        // Remove the cycle
        $graph->removeEdgeById($nodeC->getId() . "-" . $nodeA->getId());
        $this->assertFalse($graph->dfsCycleCheck($nodeA, null, $visited));
    }

    public function testTransitiveClosure()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC);

        $graph->addEdge(new Edge($nodeA, $nodeB)); // A -> B
        $graph->addEdge(new Edge($nodeB, $nodeC)); // B -> C

        $tc = $graph->transitiveClosure();

        // Since there's a path from A to B, and B to C, there should also be a path from A to C.
        $this->assertEquals(1, $tc['A']['B']);
        $this->assertEquals(1, $tc['B']['C']);
        $this->assertEquals(1, $tc['A']['C']);

        // Since it's an undirected graph, there's also a path from C to A and from C to B.
        $this->assertEquals(1, $tc['C']['A']);
        $this->assertEquals(1, $tc['C']['B']);
    }

    public function testToStringRepresentation()
    {
        $graph = new Graph();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA)
            ->addNode($nodeB)
            ->addNode($nodeC)
            ->addEdge(new Edge($nodeA, $nodeB))
            ->addEdge(new Edge($nodeB, $nodeC));

        $expectedOutput = "Graph:\n";
        $expectedOutput .= "A -> B\n";
        $expectedOutput .= "B -> A, C\n";
        $expectedOutput .= "C -> B\n";

        $this->assertEquals($expectedOutput, (string) $graph);
    }
}