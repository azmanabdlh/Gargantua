<?php

use Gargantua\PageLinked;
use Gargantua\Contract\Page;
use Gargantua\Support\Node;


function mockPageLinked() : array {
  $pageLinked = new PageLinked();

  $signUp = new class implements Page {
    public function pageName(): string {
      return "signUp";
    }

    public function onSubmit(array $payload): void {}

    public function onNext(array $payload): void {}

    public function onCompleted(): void {}

  };
  $onboarding =  new class implements Page {
    public function pageName(): string {
      return "onboarding";
    }

    public function onSubmit(array $payload): void {}

    public function onNext(array $payload): void {}

    public function onCompleted(): void {}
  };

  $pages = [$signUp, $onboarding];

  foreach ($pages as $page ) {
    $pageLinked->linked($page);
  }

  $head = new Node($signUp);
  $head->next = new Node($onboarding);
  $head->next->prev = $head;

  return [$pageLinked, $head];
}

test('valid traversal', function () {
  [$pageLinked, $node] = mockPageLinked();

  expect($pageLinked->head)->toEqual($node);
  expect($pageLinked->head->next)->toEqual($node->next);
  expect($pageLinked->head->next->prev)->toEqual($node->next->prev);
});


test('find page by name', function () {
  [$pageLinked, $node] = mockPageLinked();

  expect($pageLinked->traversalByPageName("signUp"))->toEqual($node);
  expect($pageLinked->traversalByPageName("onboarding"))->toEqual($node->next);
});


test('get first page', function () {
  [$pageLinked, $node] = mockPageLinked();

  expect($pageLinked->beginning())->toEqual($node);
});
