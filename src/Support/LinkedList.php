<?php

namespace Gargantua\Support;

use Gargantua\Contract\Page;
use Closure;

class Node {
  public function __construct(
    public ?Page $data,
    public ?Node $prev = null,
    public ?Node $next = null
  ) {}

  public function canBack(): bool {
    return $this->prev !== null;
  }

  public function canNext(): bool {
    return $this->next !== null;
  }
}

class LinkedList {

  public function __construct(
    public ?Node $head = null
  ) {}

  protected function traversal(Closure $fn): void {
    $node = $this->head;

    while ($node !== null) {
      $fn($node);
      $node = $node->next;
    }
  }

  protected function insert(Node $newNode): void {
    if ($this->head == null) {
      $this->head = &$newNode;
      return;
    }


    $node = $this->head;

    while($node->canNext()) {
      $node = $node->next;
    }


    $newNode->prev = $node;
    $node->next = $newNode;
  }
}