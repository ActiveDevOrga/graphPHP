<?php

namespace GraphPHP\Node;

class Node
{
    private string $id;
    private mixed $data;

    public function __construct(string $id, mixed $data = null) {
        $this->id = $id;
        $this->data = $data;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setData($data): Node
    {
        $this->data = $data;

        return $this;
    }
}