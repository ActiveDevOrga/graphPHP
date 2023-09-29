<?php

namespace GraphPHP\Edge;

use GraphPHP\Node\Node;

class DirectedEdge extends Edge
{
    public function getSource(): Node
    {
        return $this->nodeA;
    }

    public function getTarget(): Node
    {
        return $this->nodeB;
    }

}