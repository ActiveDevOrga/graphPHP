<?php

namespace Graph;

use GraphPHP\Edge\DirectedEdge;
use GraphPHP\Graph\DAG;
use GraphPHP\Node\Node;
use PHPUnit\Framework\TestCase;

class DAGTest extends TestCase
{
    public function testTransitiveReductionRemovesRedundantEdges()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));
        $graph->addEdge(new DirectedEdge($nodeA, $nodeC));  // Redundant edge

        $graph->transitiveReduction();

        $this->assertNull($graph->getEdge($nodeA, $nodeC));  // The direct edge from A to C should be removed.
    }

    public function testTransitiveReductionWithCycle()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeA));  // Creates a cycle

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("The graph contains a cycle.");
        $graph->transitiveReduction();
    }

    public function testTransitiveReductionNoRedundantEdges()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        $originalEdges = $graph->getEdges();
        $graph->transitiveReduction();

        $this->assertEquals($originalEdges, $graph->getEdges());  // The graph should remain unchanged.
    }

    public function testTopologicalSortOnLinearDAG()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        $sortedNodes = $graph->topologicalSort();

        // Expected order is A -> B -> C
        $this->assertEquals([$nodeA, $nodeB, $nodeC], $sortedNodes);
    }

    public function testTopologicalSortOnComplexDAG()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');
        $nodeD = new Node('D');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);
        $graph->addNode($nodeD);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeA, $nodeC));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeD));
        $graph->addEdge(new DirectedEdge($nodeC, $nodeD));

        $sortedNodes = $graph->topologicalSort();

        // A possible correct order is A -> C -> B -> D
        $this->assertEquals([$nodeA, $nodeC, $nodeB, $nodeD], $sortedNodes);
    }

    public function testTopologicalSortOnGraphWithCycle()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeA));  // Creates a cycle

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("The graph contains a cycle.");
        $graph->topologicalSort();
    }

    public function testTopologicalSortUtilOnLinearDAG()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        $stack = new \SplStack();
        $visited = [
            'A' => false,
            'B' => false,
            'C' => false
        ];

        $graph->topologicalSortUtil($nodeA, $visited, $stack);

        // The stack should contain nodes in topological order: A -> B -> C
        $this->assertEquals($nodeA, $stack->pop());
        $this->assertEquals($nodeB, $stack->pop());
        $this->assertEquals($nodeC, $stack->pop());
    }

    public function testThrowIfHasCycleForAcyclicGraph()
    {
        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));

        try {
            $graph->throwIfHasCycle(); // Should not throw an exception
            $this->assertTrue(true);  // This is a dummy assertion to indicate that the test passed.
        } catch (\Exception $e) {
            $this->fail("Exception was thrown when it shouldn't have been: " . $e->getMessage());
        }
    }

    public function testThrowIfHasCycleForCyclicGraph()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("The graph contains a cycle.");

        $graph = new DAG();

        $nodeA = new Node('A');
        $nodeB = new Node('B');
        $nodeC = new Node('C');

        $graph->addNode($nodeA);
        $graph->addNode($nodeB);
        $graph->addNode($nodeC);

        $graph->addEdge(new DirectedEdge($nodeA, $nodeB));
        $graph->addEdge(new DirectedEdge($nodeB, $nodeC));
        $graph->addEdge(new DirectedEdge($nodeC, $nodeA)); // This creates a cycle

        // Should throw an exception because of the cycle
        $graph->throwIfHasCycle();
    }
}