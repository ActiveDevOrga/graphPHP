<?php

namespace GraphPHP\Graph;


use GraphPHP\Node\Node;

class DAG extends DiGraph
{
    /**
     * Computes the transitive reduction of a directed graph.
     *
     * Transitive reduction of a graph is the graph with the minimum number of edges
     * that has the same reachability as the original graph. In other words, if there's
     * a direct edge from node A to node B, but node B is also reachable from node A
     * through other nodes, then the direct edge from A to B can be removed.
     *
     * NOTE: This function modifies the existing graph by removing certain edges.
     * If you want to keep the original graph intact, you'd need to make a copy
     * of the graph and apply the transitive reduction on the copy.
     *
     * @return DAG Returns the graph after modification.
     * @throws \Exception
     */
    public function transitiveReduction(): DAG
    {
        $this->throwIfHasCycle();

        $closure = $this->transitiveClosure();

        // Iterate over the transitive closure matrix
        foreach ($closure as $i => $row) {
            foreach ($row as $j => $val) {
                // If there's a direct edge from $i to $j
                if ($val == 1) {
                    foreach ($closure as $k => $kRow) {
                        // If there's a path from $i to $j through $k, remove the direct edge
                        if ($closure[$i][$k] == 1 && $closure[$k][$j] == 1 && $i != $k && $k != $j) {
                            $this->removeEdgeById("$i-$j");

                            break;
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Performs topological sort on the DAG.
     *
     * @return Node[] Returns an array of nodes in topological order.
     * @throws \Exception
     */
    public function topologicalSort(): array
    {
        $this->throwIfHasCycle();

        $stack = new \SplStack();
        $visited = [];

        foreach ($this->nodes as $node) {
            $visited[$node->getId()] = false;
        }

        foreach ($this->nodes as $node) {
            if (!$visited[$node->getId()]) {
                $this->topologicalSortUtil($node, $visited, $stack);
            }
        }

        $result = [];
        while (!$stack->isEmpty()) {
            $result[] = $stack->pop();
        }

        return $result;
    }

    /**
     * Recursive utility function for topologicalSort().
     *
     * @param Node $node The current node.
     * @param array $visited Array to keep track of visited nodes.
     * @param \SplStack $stack The stack to push the nodes in topological order.
     */
    private function topologicalSortUtil(Node $node, array &$visited, \SplStack $stack): void
    {
        $visited[$node->getId()] = true;

        $neighbors = $this->getNeighbors($node);
        foreach ($neighbors as $neighbor) {
            if (!$visited[$neighbor->getId()]) {
                $this->topologicalSortUtil($neighbor, $visited, $stack);
            }
        }

        $stack->push($node);
    }

    /**
     * Throw an exception if a cycle is detected
     *
     * @return void
     * @throws \Exception
     */
    private function throwIfHasCycle(): void
    {
        if ($this->hasCycle()) {
            throw new \Exception("The graph contains a cycle and cannot be simplified using transitive reduction.");
        }
    }
}