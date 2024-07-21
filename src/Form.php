<?php

namespace Gargantua;

use ValueError;
use TypeError;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

use Gargantua\Contract\Cache;
use Gargantua\Contract\Page;
use Gargantua\Contract\Request;
use Gargantua\Contract\Cable;
use Gargantua\Contract\CanNavigateBack;
use Gargantua\Cache\Cookie;
use Gargantua\Support\EventEmitter;
use Gargantua\Support\PageUtils;
use Gargantua\PageLinked;
use Gargantua\RequestType;
use Gargantua\Event;


class Form {

  private EventEmitter $emitter;

  public function __construct(
    private PageLinked $linked,
    private Cache $cache,
    private Request $request
  ) {
    $this->request = $request;
    $this->cache = $cache;
    $this->emitter = new EventEmitter();

    $this->initialize();
  }

  private function initialize(): void {
    $this->emitter->on(RequestType::Submit, function (Page $page, Request $request) {
      $page->onSubmit($request);
    });

    $this->emitter->on(RequestType::NavigateBack, function (CanNavigateBack $page) {
      $page->onNavigateBack();
    });

    $this->emitter->on(Event::Next, function (Page $page, array $payload) {
      $page->onNext($payload);
    });
  }

  public static function provide(
    array $pages = [],
    Cache $cache = new Cookie(),
    Request $request
  ): Form {
    $pageLinked = new PageLinked();

    foreach ($pages as $page) {
      if ( $page instanceof Page ) {
        $pageLinked->linked($page);
      }
    }

    if ($request == null ) {
      $request = SymfonyRequest::createFromGlobals();
    }

    return new Form(
      $pageLinked,
      $cache,
      $request
    );
  }

  public function page(): PageUtils {
    $node = $this->getCurrentNode();
    if ($node->data == null) {
      throw new ValueError("invalid instance page");
    }

    return new PageUtils(
      $node,
    );
  }

  public function capture(array $cable = []): void {
    $node = $this->getCurrentNode();
    $page = $node->data;

    if ($this->onSubmitted()) {
      foreach ($cable as $it) {
        if ( !($it instanceof Cable) ) {
          throw new TypeError("invalid argument cable type");
        }

        $it->handle($payload, fn() => $this->emitter->emit(RequestType::Submit, $page, $this->request));
      }

      if (count($cable) == 0) {
        $this->emitter->emit(RequestType::Submit, $page, $this->request);
      }

      $isCompleted = !$node->canNext();

      if ($node->canNext()) {
        $page = $node->next->data;

        $this->emitter->emit(Event::Next, $page, $this->request->all());
      };

      if ($isCompleted) {
        $this->linked->invoke("onCompleted");
        $this->cache->flush();
        return;
      }
    }

    if ($this->onNavigateBack() && ( $node->canPrev() &&  $node->prev instanceof CanNavigateBack)) {
      $page = $node->prev->data;
      $this->emitter->emit(RequestType::NavigateBack, $page);
    }


    $this->cache->store(
      "pageName",
      $page->pageName()
    );
  }

  private function getCurrentNode(): Node {
    $pageName = $this->cache->get("pageName");
    $node = $this->linked->beginning();

    if ($curPageName != "") {
      $node = $this->linked->traversalByPageName($pageName);
    }

    return $node;
  }


  public function onSubmitted(): bool {
    return $this->request->isMethod("POST") && $this->request->get(RequestType::Submit->toString());
  }

  public function onNavigateBack(): bool {
    return $this->request->isMethod("POST") && $this->request->get(RequestType::NavigateBack->toString());
  }
}