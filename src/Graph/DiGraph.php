<?php

namespace GraphPHP\Graph;

use Exception;
use GraphPHP\Edge\DirectedEdge;
use GraphPHP\Edge\Edge;
use GraphPHP\Node\Node;

/**
 * Class DiGraph
 * Represents a Directed Graph that extends the basic Graph class.
 */
class DiGraph extends Graph
{
    /** @var DirectedEdge[] Array of directed edges in the graph, indexed by edge ID */
    protected array $edges = [];

    /**
     * Overrides the addEdge method from the parent Graph class.
     * Ensures that only directed edges can be added to the directed graph.
     *
     * @param Edge $edge The edge to be added.
     * @return DiGraph Returns the current directed graph instance for method chaining.
     * @throws Exception Exception Throws an exception if an undirected edge is attempted to be added.
     */
    public function addEdge(Edge $edge): DiGraph
    {
        if (!($edge instanceof DirectedEdge)) {
            throw new \Exception("Only directed edges are allowed in a directed graph");
        }

        return parent::addEdge($edge);
    }

    /**
     * Retrieves an edge between two given nodes.
     *
     * @param Node $nodeA Source node.
     * @param Node $nodeB Target node.
     * @return DirectedEdge|null Returns the edge if found, otherwise null.
     */
    public function getEdge(Node $nodeA, Node $nodeB): ?Edge
    {
        foreach ($this->edges as $edge) {
            list($edgeNodeA, $edgeNodeB) = $edge->getNodes();
            if ($edgeNodeA->getId() === $nodeA->getId() && $edgeNodeB->getId() === $nodeB->getId()) {
                return $edge;
            }
        }
        return null;
    }

    /**
     * Retrieves the outgoing neighbors (nodes reached by outgoing edges) of a given node.
     *
     * @param Node $node The node whose outgoing neighbors are to be retrieved.
     * @return Node[] Returns an array of neighboring nodes.
     */
    public function getNeighbors(Node $node): array
    {
        $neighbors = [];

        foreach ($this->edges as $edge) {
            if ($edge->getSource()->getId() === $node->getId()) {
                $neighbors[] = $edge->getTarget();
            }
        }

        return $neighbors;
    }

    /**
     * Retrieves the predecessors of a node (nodes that have directed edges pointing to the given node).
     *
     * @param Node $node The node whose predecessors are to be retrieved.
     * @return Node[] Returns an array of predecessor nodes.
     */
    public function getPredecessors(Node $node): array
    {
        $predecessors = [];

        foreach ($this->edges as $edge) {
            if ($edge->getTarget()->getId() === $node->getId()) {
                $predecessors[] = $edge->getSource();
            }
        }

        return $predecessors;
    }

    /**
     * Constructs and returns the adjacency matrix of the directed graph.
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
                $edge = $this->getEdgeById("$sourceId-$targetId");
                if ($edge !== null) {
                    $matrix[$sourceId][$targetId] = $edge->getWeight() ?? 0.0;
                }
            }
        }

        return $matrix;
    }

    /**
     * Determines if the directed graph contains a cycle.
     *
     * @return bool Returns true if a cycle exists, otherwise false.
     */
    public function hasCycle(): bool
    {
        $visited = [];
        $recursionStack = []; // Used to keep track of nodes in the current path

        foreach ($this->nodes as $node) {
            $visited[$node->getId()] = false;
            $recursionStack[$node->getId()] = false;
        }

        foreach ($this->nodes as $node) {
            if (!$visited[$node->getId()]) {
                if ($this->dfsCycleCheckForDiGraph($node, $visited, $recursionStack)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Depth First Search utility function to check for cycles in directed graph.
     *
     * @param Node $node The current node being visited.
     * @param array $visited Array to keep track of visited nodes.
     * @param array $recursionStack Array to keep track of nodes in the current path.
     * @return bool Returns true if a cycle is detected, otherwise false.
     */
    public function dfsCycleCheckForDiGraph(Node $node, array &$visited, array &$recursionStack): bool
    {
        $nodeId = $node->getId();
        $visited[$nodeId] = true;
        $recursionStack[$nodeId] = true;

        $neighbors = $this->getNeighbors($node);
        foreach ($neighbors as $neighbor) {
            $neighborId = $neighbor->getId();

            if (!$visited[$neighborId]) {
                if ($this->dfsCycleCheckForDiGraph($neighbor, $visited, $recursionStack)) {
                    return true;
                }
            } elseif ($recursionStack[$neighborId]) {
                // If the neighbor is in the recursion stack, a cycle is found
                return true;
            }
        }

        $recursionStack[$nodeId] = false; // Remove the node from the current path before returning
        return false;
    }

    /**
     * Computes the shortest path from a source node to all other nodes using the Bellman-Ford algorithm.
     * The algorithm can also detect negative weight cycles in the graph.
     *
     * @param Node $source The source node from which shortest paths are to be computed.
     * @return array Returns an associative array with keys 'distances' and 'previous' indicating shortest path distances
     *               and the previous node in the path respectively.
     * @throws Exception Throws an exception if a negative weight cycle is detected.
     */
    public function bellmanFord(Node $source): array
    {
        $distances = [];
        $previous = [];

        // Step 1: Initialize distances and predecessors
        foreach ($this->nodes as $node) {
            $distances[$node->getId()] = INF;
            $previous[$node->getId()] = null;
        }
        $distances[$source->getId()] = 0;

        // Step 2: Relax edges repeatedly
        $numNodes = count($this->nodes);
        for ($i = 0; $i < $numNodes - 1; $i++) {
            foreach ($this->edges as $edge) {
                $u = $edge->getSource()->getId();
                $v = $edge->getTarget()->getId();
                $weight = $edge->getWeight();
                if ($distances[$u] != INF && $distances[$u] + $weight < $distances[$v]) {
                    $distances[$v] = $distances[$u] + $weight;
                    $previous[$v] = $u;
                }
            }
        }

        // Step 3: Check for negative weight cycles
        foreach ($this->edges as $edge) {
            $u = $edge->getSource()->getId();
            $v = $edge->getTarget()->getId();
            if ($distances[$u] != INF && $distances[$u] + $edge->getWeight() < $distances[$v]) {
                throw new \Exception("Graph contains a negative weight cycle");
            }
        }

        return ['distances' => $distances, 'previous' => $previous];
    }

    /**
     * Computes the shortest path from a source node to a destination node using the Bellman-Ford algorithm.
     * The algorithm can also detect negative weight cycles in the graph.
     *
     * @param Node $source The source node from which the path starts.
     * @param Node $destination The destination node where the path ends.
     * @return array Returns an associative array with keys 'path' and 'cost' indicating the shortest path and its cost.
     * @throws Exception Throws an exception if a negative weight cycle is detected.
     */
    public function shortestPathBellmanFord(Node $source, Node $destination): array
    {
        $result = $this->bellmanFord($source);

        // If destination node is not reachable, return an empty path and INF cost
        if ($result['distances'][$destination->getId()] == INF) {
            return ['path' => [], 'cost' => INF];
        }

        // Reconstruct the path from destination to source using the 'previous' data
        $path = [];
        $current = $destination->getId();
        while ($current !== null) {
            $path[] = $current;
            $current = $result['previous'][$current];
        }

        // The path is constructed in reverse, so we need to reverse it to get the correct order
        $path = array_reverse($path);

        return [
            'path' => $path,
            'cost' => $result['distances'][$destination->getId()]
        ];
    }
}