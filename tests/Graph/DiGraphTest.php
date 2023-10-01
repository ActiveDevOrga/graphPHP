<?php

namespace Graph;

use PHPUnit\Framework\TestCase;
use GraphPHP\Graph\DiGraph;
use GraphPHP\Node\Node;
use GraphPHP\Edge\DirectedEdge;
use GraphPHP\Edge\Edge;


class DiGraphTest extends TestCase
{
    public function testOnlyDirectedEdgesAllowed()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');

        // Testing with directed edge (should not throw exception)
        $directedEdge = new DirectedEdge($nodeA, $nodeB);
        $graph->addEdge($directedEdge);

        // Testing with undirected edge (should throw exception)
        $this->expectException(\Exception::class);
        $undirectedEdge = new Edge($nodeA, $nodeB);
        $graph->addEdge($undirectedEdge);
    }

    public function testGetEdge()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new DirectedEdge($nodeA, $nodeB);
        $graph->addEdge($edge);

        $retrievedEdge = $graph->getEdge($nodeA, $nodeB);
        $this->assertSame($edge, $retrievedEdge);
    }

    public function testGetEdgeNoEdgeFound()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');

        $retrievedEdge = $graph->getEdge($nodeA, $nodeB);
        $this->assertNull($retrievedEdge);
    }

    public function testGetNeighbors()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $edge = new DirectedEdge($nodeA, $nodeB);
        $graph->addEdge($edge);

        $neighbors = $graph->getNeighbors($nodeA);
        $this->assertCount(1, $neighbors);
        $this->assertSame($nodeB, $neighbors[0]);
    }

    public function testGetPredecessors()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addEdge(new DirectedEdge($nodeA, $nodeC));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));
        $graph->addEdge(new DirectedEdge($nodeD, $nodeC));

        $predecessors = $graph->getPredecessors($nodeC);

        $this->assertCount(3, $predecessors); // There are 3 nodes pointing to node C
        $this->assertContains($nodeA, $predecessors); // Node A is a predecessor of Node C
        $this->assertContains($nodeB, $predecessors); // Node B is a predecessor of Node C
        $this->assertContains($nodeD, $predecessors); // Node D is a predecessor of Node C

        $predecessorsA = $graph->getPredecessors($nodeA);
        $this->assertCount(0, $predecessorsA); // No nodes pointing to node A
    }

    public function testGetAdjacencyMatrix()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);
        $graph->addNode($nodeD);
        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 1.0));
        $graph->addEdge(new DirectedEdge($nodeA, $nodeC, 2.0));
        $graph->addEdge(new DirectedEdge($nodeC, $nodeD, 3.0));

        $matrix = $graph->getAdjacencyMatrix();

        $expectedMatrix = [
            'A' => ['A' => false, 'B' => 1.0,   'C' => 2.0,   'D' => false],
            'B' => ['A' => false, 'B' => false, 'C' => false, 'D' => false],
            'C' => ['A' => false, 'B' => false, 'C' => false, 'D' => 3.0],
            'D' => ['A' => false, 'B' => false, 'C' => false, 'D' => false]
        ];

        $this->assertEquals($expectedMatrix, $matrix);
    }

    public function testHasCycle()
    {
        // Graph with a cycle: A -> B -> C -> A
        $graphWithCycle = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $graphWithCycle->addNode($nodeA);
        $graphWithCycle->addNode($nodeB);
        $graphWithCycle->addNode($nodeC);
        $graphWithCycle->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graphWithCycle->addEdge(new DirectedEdge($nodeB, $nodeC));
        $graphWithCycle->addEdge(new DirectedEdge($nodeC, $nodeA));

        $this->assertTrue($graphWithCycle->hasCycle());

        // Graph without a cycle: A -> B -> C
        $graphWithoutCycle = new DiGraph();
        $graphWithoutCycle->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graphWithoutCycle->addEdge(new DirectedEdge($nodeB, $nodeC));

        $this->assertFalse($graphWithoutCycle->hasCycle());
    }

    public function testDfsCycleCheckForDiGraph()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        // Initialize the visited and recursion stack
        $visited = [
            'A' => false,
            'B' => false,
            'C' => false
        ];
        $recursionStack = [
            'A' => false,
            'B' => false,
            'C' => false
        ];

        $this->assertFalse($graph->dfsCycleCheckForDiGraph($nodeA, $visited, $recursionStack));

        // Add an edge to create a cycle: C -> A
        $graph->addEdge(new DirectedEdge($nodeC, $nodeA));

        // Reset the visited and recursion stack for the new graph structure
        $visited = [
            'A' => false,
            'B' => false,
            'C' => false
        ];
        $recursionStack = [
            'A' => false,
            'B' => false,
            'C' => false
        ];

        $this->assertTrue($graph->dfsCycleCheckForDiGraph($nodeA, $visited, $recursionStack));
    }

    public function testBellmanFordNoNegativeWeights()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));
        $graph->addEdge(new DirectedEdge($nodeA, $nodeC, 10.0));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC, 2.0));

        $result = $graph->bellmanFord($nodeA);

        $this->assertEquals(['A' => 0, 'B' => 5, 'C' => 7], $result['distances']);
        $this->assertEquals(['A' => null, 'B' => 'A', 'C' => 'B'], $result['previous']);
    }

    public function testBellmanFordNoWeights()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeA, $nodeC));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        $result = $graph->bellmanFord($nodeA);

        $this->assertEquals(['A' => 0, 'B' => 0, 'C' => 0], $result['distances']);
        $this->assertEquals(['A' => null, 'B' => 'A', 'C' => 'A'], $result['previous']);
    }

    public function testBellmanFordNegativeWeightsNoCycle()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));
        $graph->addEdge(new DirectedEdge($nodeA, $nodeC, 10.0));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC, -7.0));

        $result = $graph->bellmanFord($nodeA);

        $this->assertEquals(['A' => 0, 'B' => 5, 'C' => -2], $result['distances']);
        $this->assertEquals(['A' => null, 'B' => 'A', 'C' => 'B'], $result['previous']);
    }

    public function testBellmanFordNegativeCycle()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC, -7.0));
        $graph->addEdge(new DirectedEdge($nodeC, $nodeA, -3.0));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Graph contains a negative weight cycle");

        $graph->bellmanFord($nodeA);
    }

    public function testShortestPathBellmanFordNoNegativeWeights()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC, 2.0));

        $result = $graph->shortestPathBellmanFord($nodeA, $nodeC);

        $this->assertEquals(['A', 'B', 'C'], $result['path']);
        $this->assertEquals(7.0, $result['cost']);
    }

    public function testShortestPathBellmanFordNoWeights()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        $result = $graph->shortestPathBellmanFord($nodeA, $nodeC);

        $this->assertEquals(['A', 'B', 'C'], $result['path']);
        $this->assertEquals(0.0, $result['cost']);
    }

    public function testShortestPathBellmanFordUnreachableDestination()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));

        $result = $graph->shortestPathBellmanFord($nodeA, $nodeC);

        $this->assertEquals([], $result['path']);
        $this->assertEquals(INF, $result['cost']);
    }

    public function testShortestPathBellmanFordNegativeWeightsNoCycle()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC, -7.0));

        $result = $graph->shortestPathBellmanFord($nodeA, $nodeC);

        $this->assertEquals(['A', 'B', 'C'], $result['path']);
        $this->assertEquals(-2.0, $result['cost']);
    }

    public function testShortestPathBellmanFordNegativeCycle()
    {
        $graph = new DiGraph();
        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB, 5.0));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC, -7.0));
        $graph->addEdge(new DirectedEdge($nodeC, $nodeA, -3.0));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Graph contains a negative weight cycle");

        $graph->shortestPathBellmanFord($nodeA, $nodeC);
    }
}