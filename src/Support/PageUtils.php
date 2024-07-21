<?php

namespace Gargantua\Support;

use Gargantua\Contract\Page;
use Gargantua\Contract\CanNvigateBack;

class PageUtils {

  public function __construct(
    private Node $head
  ) {}

  public function is(string $pageName): bool {
    return $this->page->pageName() == $pageName;
  }


  public function last(): bool {
    return !$this->head->canNext();
  }

  public function canBack(): bool {
    return $this->head->canPrev() &&
    ($this->head->prev instanceof CanNvigateBack);
  }
}