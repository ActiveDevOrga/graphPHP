# GraphPHP

A PHP graph theory package that provides structures and algorithms for working with graphs.

## Installation

You can install the package via Composer:

```bash
composer require actived/graphphp
```

# Features
## Graph

The `Graph` class provides the foundation for working with undirected graphs in PHP. It includes methods for manipulating nodes, edges, and retrieving various properties of the graph.

### Creating a Graph

```php
use GraphPHP\Graph\Graph;
use GraphPHP\Node\Node;
use GraphPHP\Edge\Edge;

$graph = new Graph();
```

### Adding Nodes
```php
$nodeA = new Node('A');
$graph->addNode($nodeA);
```

### Adding Edges
```php
$nodeB = new Node('B');
$edge = new Edge($nodeA, $nodeB);
$graph->addEdge($edge);
```

### Removing Nodes and Edges
Nodes and edges can be removed from the graph:
```php
$graph->removeNode($nodeA);
$graph->removeEdge($edge);
```

### Neighbors
Retrieve the neighbors of a given node:
```php
$neighbors = $graph->getNeighbors($nodeB);
```

### Adjacency Matrix
Get the adjacency matrix of the graph:
```php
$matrix = $graph->getAdjacencyMatrix();
```

### Checking for Cycles
Determine if the graph contains a cycle:
```php
if ($graph->hasCycle()) {
    echo "The graph has a cycle.";
} else {
    echo "The graph does not have a cycle.";
}
```

### Transitive Closure
Compute the transitive closure of the graph using the Floyd-Warshall algorithm:
```php
$closure = $graph->transitiveClosure();
```

### Shortest Path
Compute the shortest path between two nodes using Dijkstra's algorithm:
```php
$pathInfo = $graph->shortestPathDijkstra($nodeA, $nodeC);
echo "Shortest path: " . implode(' -> ', $pathInfo['path']);
echo "Cost: " . $pathInfo['cost'];
```

### String Representation
To get a string representation of the graph:
```php
echo $graph;
```
Note: The Graph class assumes an undirected graph. For directed graphs, refer to the DiGraph class documentation.

## DiGraph (Directed Graph)

The `DiGraph` class extends the base `Graph` class and represents a directed graph. This means all edges in this graph have a direction, going from a source node to a target node.

### Creating a Directed Graph

```php
use GraphPHP\Graph\DiGraph;
use GraphPHP\Node\Node;
use GraphPHP\Edge\DirectedEdge;

$diGraph = new DiGraph();
```

### Adding Directed Edges
Only directed edges can be added to a directed graph:
```php
$nodeA = new Node('A');
$nodeB = new Node('B');
$directedEdge = new DirectedEdge($nodeA, $nodeB);
$diGraph->addEdge($directedEdge);
```

### Outgoing Neighbors
Retrieve the outgoing neighbors of a given node:
```php
$outgoingNeighbors = $diGraph->getNeighbors($nodeA);
```

### Predecessors
Retrieve the predecessors (nodes with directed edges pointing to the given node) of a node:
```php
$predecessors = $diGraph->getPredecessors($nodeB);
```

### Bellman-Ford Shortest Path
Compute the shortest path between two nodes using the Bellman-Ford algorithm:
```php
$pathInfo = $diGraph->shortestPathBellmanFord($nodeA, $nodeC);
echo "Shortest path: " . implode(' -> ', $pathInfo['path']);
echo "Cost: " . $pathInfo['cost'];
```

### Checking for Cycles in Directed Graphs
Determine if the directed graph contains a cycle:
```php
if ($diGraph->hasCycle()) {
    echo "The directed graph has a cycle.";
} else {
    echo "The directed graph does not have a cycle.";
}
```

### Adjacency Matrix for Directed Graphs
Get the adjacency matrix of the directed graph:
```php
$matrix = $diGraph->getAdjacencyMatrix();
```

Note: The DiGraph class is specific to directed graphs. If you need an undirected graph, refer to the base Graph class documentation.

## Directed Acyclic Graphs (DAG)
Create and manipulate directed acyclic graphs.
```php
use GraphPHP\Graph\DAG;
use GraphPHP\Node\Node;
use GraphPHP\Edge\DirectedEdge;

$graph = new DAG();
$nodeA = new Node('A');
$nodeB = new Node('B');
$nodeC = new Node('C');

$graph->addNode($nodeA)
    ->addNode($nodeB)
    ->addNode($nodeC)
    ->addEdge(new DirectedEdge($nodeA, $nodeB, 4))
    ->addEdge(new DirectedEdge($nodeB, $nodeC, -6))
    ->addEdge(new DirectedEdge($nodeA, $nodeC, 2));
```

### Transitive Reduction
Perform transitive reduction on a DAG.
```php
$graph->transitiveReduction();
echo $graph; // Visual representation of the graph
```

### Topological Sort
Get a topological ordering of the nodes in a DAG.
```php
$order = $graph->topologicalSort();
print_r($order);
```

# Roadmap
- Testing: Implement comprehensive tests for the current functionalities.
- Trees: Introduce tree graph structures.
- Directed Trees: Extend the tree structures to support directed trees.
- Binary Trees: Implement binary tree structures and related algorithms.

# Contributing
If you have suggestions or improvements, feel free to submit a pull request or open an issue on the GitHub repository.

# License
This package is open-sourced software licensed under the MIT license.