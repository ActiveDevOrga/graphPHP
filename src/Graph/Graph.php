<?php

namespace GraphPHP\Graph;

use Exception;
use GraphPHP\Edge\Edge;
use GraphPHP\Node\Node;

class Graph
{
    /** @var Node[] Array of nodes in the graph, indexed by node ID */
    protected array $nodes = [];

    /** @var Edge[] Array of edges in the graph, indexed by edge ID */
    protected array $edges = [];

    /**
     * Adds a node to the graph.
     *
     * @param Node $node Node to be added.
     * @return Graph Returns the current graph instance for method chaining.
     * @throws Exception Throws an exception if a node with the same ID already exists.
     */
    public function addNode(Node $node): Graph
    {
        if (isset($this->nodes[$node->getId()])) {
            throw new Exception("A node with the ID '{$node->getId()}' already exists.");
        }

        $this->nodes[$node->getId()] = $node;

        return $this;
    }

    /**
     * Removes a node and its associated edges from the graph.
     *
     * @param Node $node Node to be removed.
     * @return Graph Returns the current graph instance for method chaining.
     */
    public function removeNode(Node $node): Graph
    {
        unset($this->nodes[$node->getId()]);

        $neighbors = $this->getNeighbors($node);

        foreach ($neighbors as $neighbor) {
            foreach ($this->edges as $edgeId => $edge) {
                list($nodeA, $nodeB) = $edge->getNodes();
                if (($nodeA->getId() === $node->getId() && $nodeB->getId() === $neighbor->getId()) ||
                    ($nodeB->getId() === $node->getId() && $nodeA->getId() === $neighbor->getId())) {
                    unset($this->edges[$edgeId]);
                }
            }
        }

        return $this;
    }

    /**
     * Adds an edge to the graph.
     *
     * @param Edge $edge Edge to be added.
     * @return Graph Returns the current graph instance for method chaining.
     * @throws Exception Throws an exception if an edge with the same ID already exists.
     */
    public function addEdge(Edge $edge): Graph
    {
        if (isset($this->edges[$edge->getId()])) {
            throw new Exception("An edge with the ID '{$edge->getId()}' already exists.");
        }

        $this->edges[$edge->getId()] = $edge;

        return $this;
    }

    /**
     * Removes an edge from the graph.
     *
     * @param Edge $edgeToRemove Edge to be removed.
     * @return Graph Returns the current graph instance for method chaining.
     */
    public function removeEdge(Edge $edgeToRemove): Graph
    {
        unset($this->edges[$edgeToRemove->getId()]);

        return $this;
    }

    /**
     * Removes an edge from the graph by its ID.
     *
     * @param string $edgeId ID of the edge to be removed.
     * @return Graph Returns the current graph instance for method chaining.
     */
    public function removeEdgeById(string $edgeId): Graph
    {
        unset($this->edges[$edgeId]);

        return $this;
    }

    /**
     * Retrieves an edge from the graph by its ID.
     *
     * @param string $edgeId ID of the edge to retrieve.
     * @return Edge|null Returns the corresponding edge.
     */
    public function getEdgeById(string $edgeId): ?Edge
    {
        if (!array_key_exists($edgeId, $this->edges)) {
            return null;
        }

        return $this->edges[$edgeId];
    }

    /**
     * Retrieves the neighbors (adjacent nodes) of a given node.
     *
     * @param Node $node Node whose neighbors are to be retrieved.
     * @return Node[] Returns an array of neighboring nodes.
     */
    public function getNeighbors(Node $node): array
    {
        $neighbors = [];

        foreach ($this->edges as $edge) {
            list($nodeA, $nodeB) = $edge->getNodes();

            if ($nodeA->getId() === $node->getId()) {
                $neighbors[] = $nodeB;
            } elseif ($nodeB->getId() === $node->getId()) {
                $neighbors[] = $nodeA;
            }
        }

        return $neighbors;
    }

    /**
     * Constructs and returns the adjacency matrix of the graph.
     *
     * @return array Returns the adjacency matrix.
     */
    public function getAdjacencyMatrix(): array
    {
        // Initialize the matrix with 'false'
        $matrix = [];

        // Populate the matrix
        foreach ($this->nodes as $sourceId => $sourceNode) {
            $matrix[$sourceId] = [];

            foreach ($this->nodes as $targetId => $targetNode) {
                // Initialize with 'false'
                $matrix[$sourceId][$targetId] = false;

                // Check if there's an edge from sourceNode to targetNode
                $edge = $this->getEdge($sourceNode, $targetNode);
                if ($edge !== null) {
                    $matrix[$sourceId][$targetId] = $edge->getWeight() ?? 0.0;

                    // Since it's an undirected graph, the edge is mirrored
                    $matrix[$targetId][$sourceId] = $matrix[$sourceId][$targetId];
                }
            }
        }

        return $matrix;
    }

    /**
     * Computes the shortest path from a source node to a target node using Dijkstra's algorithm.
     * This optimized implementation uses a priority queue and handles zero weights.
     *
     * @param Node $start Source node.
     * @param Node $end Target node.
     * @return array Returns an associative array with 'path' and 'cost' keys.
     * @throws Exception
     */
    public function shortestPathDijkstra(Node $start, Node $end): array
    {
        // Check if the graph contains negative weights
        if ($this->containsNegativeWeight()) {
            throw new \Exception("Dijkstra's algorithm cannot handle graphs with negative edge weights.");
        }

        $distances = [];
        $previous = [];
        $nodes = $this->nodes;

        foreach ($nodes as $node) {
            $distances[$node->getId()] = INF;
            $previous[$node->getId()] = null;
        }
        $distances[$start->getId()] = 0;

        $queue = new \SplPriorityQueue();
        foreach ($distances as $nodeId => $distance) {
            $queue->insert($nodeId, -$distance);
        }

        $visited = []; // To keep track of visited nodes

        while (!$queue->isEmpty()) {
            $currentNodeId = $queue->extract();

            // If the node has already been visited, skip
            if (isset($visited[$currentNodeId])) {
                continue;
            }

            $visited[$currentNodeId] = true; // Mark the node as visited

            if ($currentNodeId === $end->getId()) {
                break;
            }

            $neighbors = $this->getNeighbors($nodes[$currentNodeId]);
            foreach ($neighbors as $neighbor) {
                if (isset($visited[$neighbor->getId()])) { // Skip if neighbor is visited
                    continue;
                }

                $weight = $this->getEdgeWeight($nodes[$currentNodeId], $neighbor);
                $alt = $distances[$currentNodeId] + $weight;

                if ($alt < $distances[$neighbor->getId()]) {
                    $distances[$neighbor->getId()] = $alt;
                    $previous[$neighbor->getId()] = $currentNodeId;

                    // Update priority for the neighbor
                    $queue->insert($neighbor->getId(), -$alt);
                }
            }
        }

        $path = [];
        $current = $end->getId();
        while ($current !== null) {
            array_unshift($path, $current);
            $current = $previous[$current];
        }

        $cost = $distances[$end->getId()];

        return [
            'path' => ($path[0] === $start->getId()) ? $path : [],
            'cost' => $cost
        ];
    }

    /**
     * Retrieves an edge between two given nodes.
     *
     * @param Node $nodeA First node.
     * @param Node $nodeB Second node.
     * @return Edge|null Returns the edge if found, otherwise null.
     */
    protected function getEdge(Node $nodeA, Node $nodeB): ?Edge
    {
        foreach ($this->edges as $edge) {
            list($edgeNodeA, $edgeNodeB) = $edge->getNodes();
            if (($edgeNodeA->getId() === $nodeA->getId() && $edgeNodeB->getId() === $nodeB->getId()) ||
                ($edgeNodeB->getId() === $nodeA->getId() && $edgeNodeA->getId() === $nodeB->getId())) {
                return $edge;
            }
        }
        return null;
    }

    /**
     * Retrieves the weight of an edge between two given nodes.
     *
     * @param Node $nodeA First node.
     * @param Node $nodeB Second node.
     * @return float Returns the weight of the edge, or INF if no edge exists.
     */
    private function getEdgeWeight(Node $nodeA, Node $nodeB): float
    {
        $edge = $this->getEdge($nodeA, $nodeB);

        if ($edge !== null) {
            return $edge->getWeight();
        }

        return INF;
    }

    /**
     * Checks if the graph contains an edge with a negative weight.
     *
     * @return bool Returns true if the graph contains a negative weight edge, otherwise false.
     */
    public function containsNegativeWeight(): bool
    {
        foreach ($this->edges as $edge) {
            if ($edge->getWeight() < 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines if the graph contains a cycle.
     *
     * @return bool Returns true if a cycle exists, otherwise false.
     */
    public function hasCycle(): bool
    {
        $visited = [];
        foreach ($this->nodes as $node) {
            $visited[$node->getId()] = false;
        }

        foreach ($this->nodes as $node) {
            if (!$visited[$node->getId()]) {
                if ($this->dfsCycleCheck($node, null, $visited)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Depth First Search utility function to check for cycles.
     *
     * @param Node $current The current node being visited.
     * @param Node|null $parent The parent node of the current node.
     * @param array $visited Array to keep track of visited nodes.
     * @return bool Returns true if a cycle is detected, otherwise false.
     */
    private function dfsCycleCheck(Node $current, ?Node $parent, array &$visited): bool
    {
        $visited[$current->getId()] = true;

        $neighbors = $this->getNeighbors($current);
        foreach ($neighbors as $neighbor) {
            if (!$visited[$neighbor->getId()]) {
                if ($this->dfsCycleCheck($neighbor, $current, $visited)) {
                    return true;
                }
            } elseif ($parent === null || $neighbor->getId() !== $parent->getId()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns a string representation of the graph.
     *
     * @return string The string representation of the graph.
     */
    public function __toString(): string {
        $output = "Graph:\n";

        // Iterate through each node in the graph
        foreach ($this->nodes as $node) {
            $nodeId = $node->getId();

            // Fetch the neighbors for the current node
            $neighbors = $this->getNeighbors($node);

            // Convert the list of neighbor nodes to their IDs
            $neighborIds = array_map(function($neighbor) {
                return $neighbor->getId();
            }, $neighbors);

            // Append the current node and its neighbors to the output
            $output .= "$nodeId -> " . implode(', ', $neighborIds) . "\n";
        }

        return $output;
    }

    /**
     * Helper function to find the ID of an edge between two nodes.
     *
     * @param string $sourceId ID of the source node.
     * @param string $targetId ID of the target node.
     * @return string|null Returns the ID of the edge if found, otherwise null.
     */
    public function findEdgeId(string $sourceId, string $targetId): ?string
    {
        foreach ($this->edges as $edge) {
            if ($edge->getNodeA()->getId() === $sourceId && $edge->getNodeB()->getId() === $targetId) {
                return $edge->getId();
            }
        }
        return null;
    }

    public function transitiveClosure(): array
    {
        $adjacencyMatrix = $this->getAdjacencyMatrix();
        $tc = [];

        // Initialize tc with the binary representation of the adjacency matrix
        foreach ($adjacencyMatrix as $sourceId => $neighbors) {
            $tc[$sourceId] = [];
            foreach ($neighbors as $targetId => $weight) {
                $tc[$sourceId][$targetId] = $weight !== false ? 1 : 0;
            }
        }

        $nodeIds = array_keys($this->nodes);

        // Floyd-Warshall
        foreach ($nodeIds as $k) {
            foreach ($nodeIds as $i) {
                foreach ($nodeIds as $j) {
                    $tc[$i][$j] = $tc[$i][$j] || ($tc[$i][$k] && $tc[$k][$j]);
                }
            }
        }

        return $tc;
    }
}