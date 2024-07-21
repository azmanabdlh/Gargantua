<?php

namespace Gargantua;

use ValueError;

use Gargantua\Contract\Page;
use Gargantua\Support\LinkedList;
use Gargantua\Support\Node;

class PageLinked extends LinkedList {

  public function traversalByPageName(string $pageName) {
    $pageNode = null;
    $this->traversal(function (Node $node) use (&$pageNode, $pageName) {
      if ($node->data->pageName() == $pageName) {
        $pageNode = $node;
      }
    });

    if ($pageNode == null) {
      throw new ValueError($pageName ." page not found");
    }

    return $pageNode;
  }

  public function invoke(string $event, array $data = []): void {
    $this->traversal(function (Node $node) use (&$event, &$data) {
      call_user_func_array([$node->data, $event], [$data]);
    });
  }

  public function beginning(): Node {
    if ( $this->head != null ) {
      return $this->head;
    }

    throw new ValueError("empty page");
  }

  public function linked(Page $page): void {
    $node = new Node($page);
    parent::insert($node);
  }
}