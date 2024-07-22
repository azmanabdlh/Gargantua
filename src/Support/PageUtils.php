<?php

namespace Gargantua\Support;

use Gargantua\Contract\Page;
use Gargantua\Contract\CanLabel;
use Gargantua\Contract\CanNvigateBack;

class PageUtils {

  public function __construct(
    private int $pageNumber,
    private Page $page
  ) {}

  public function is(string $pageName): bool {
    return $this->page->pageName() == $pageName;
  }


  public function icon(): string {
    if ($this->page instanceof CanLabel) {
      return $this->page->icon();
    }

    return "";
  }

  public function label(): string {
    if ($this->page instanceof CanLabel) {
      return $this->page->label();
    }

    return "";
  }

  public function number(): int {
    return $this->pageNumber;
  }
}