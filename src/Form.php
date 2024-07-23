<?php

namespace Gargantua;

use ValueError;
use TypeError;

use Gargantua\Contract\Session;
use Gargantua\Contract\Page;
use Gargantua\Contract\Cable;
use Gargantua\Contract\CanNavigateBack;
use Gargantua\Support\PageUtils;
use Gargantua\Support\Node;
use Gargantua\PageLinked;
use Gargantua\Event;
use Gargantua\EventEmitter;
use Gargantua\CookieSessionHandler;
use Gargantua\Http\Request;


class Form {

  private EventEmitter $emitter;

  private Request $request;

  public function __construct(
    private PageLinked $linked,
    private Session $session
  ) {
    $this->request = Request::createFromGlobals();
    $this->emitter = new EventEmitter();

    $this->initialize();
  }

  private function initialize(): void {
    $this->emitter->on(Event::RequestSubmit, function (Page $page, Request $request) {
      $page->onSubmit($request);
    });

    $this->emitter->on(Event::RequestBack, function (CanNavigateBack $page) {
      $page->onNavigateBack();
    });

    $this->emitter->on(Event::Next, function (Page $page, array $payload) {
      $page->onNext($payload);
    });
  }

  public static function provide(
    array $pages,
    Session $session = null,
  ): Form {
    $pageLinked = new PageLinked();

    foreach ($pages as $idx => $page) {
      if ( !($page instanceof Page) ) {
        throw TypeError("invalid instance page");
      }

      $node = new Node($page, $idx);
      $pageLinked->insert($node);
    }

    if ($session == null) {
      $session = new CookieSessionHandler(
        $pageLinked->beginning()
      );
    }

    return new Form(
      $pageLinked,
      $session,
    );
  }

  public function page(): PageUtils {
    $node = $this->getCurrentNode();
    if ($node->data == null) {
      throw new ValueError("invalid instance page");
    }

    return new PageUtils(
      $node->idx + 1,
      $node->data,
    );
  }

  public function canBack(): bool {
    $node = $this->getCurrentNode();

    return $node->canBack();
  }

  public function last(): bool {
    $node = $this->getCurrentNode();

    return !$node->canNext();
  }

  public function capture(array $cable = []): void {
    $node = $this->getCurrentNode();
    $page = $node->data;

    if ($this->request->onSubmitted()) {
      foreach ($cable as $it) {
        if ( !($it instanceof Cable) ) {
          continue;
        }

        $it->handle($this->request);
      }

      $this->emitter->emit(Event::RequestSubmit, $page, $this->request);

      $isCompleted = $node->canNext();

      if ($node->canNext()) {
        $page = $node->next->data;

        $this->emitter->emit(Event::Next, $page, $this->request->getPayload()->all());
      };

      if ($isCompleted) {
        $this->linked->invoke("onCompleted");
        $this->session->flush();
        return;
      }
    }

    if ($this->request->onBack() && $node->canBack() ) {
      $page = $node->prev->data;
      $this->emitter->emit(Event::RequestBack, $page);
    }

    $this->session->store($page->pageName());
  }

  private function getCurrentNode(): Node {
    $pageName = $this->session->get();
    $node = $this->linked->beginning();

    if ($pageName != "") {
      $node = $this->linked->traversalByPageName($pageName);
    }

    return $node;
  }
}