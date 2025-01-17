<?php


namespace Gargantua;


use Symfony\Component\HttpFoundation\Cookie;

use Gargantua\Http\Request;
use Gargantua\Http\Response;
use Gargantua\Contract\Session;
use Gargantua\Support\Node;


class CookieSessionHandler implements Session {

  private Request $request;

  private Response $response;

  private array $pages = [];

  private const CurrPageKey = "gargantua:page:current";

  public function __construct(
    Node $node
  ) {
    $this->response = new Response();
    $this->request = Request::createFromGlobals();

    $this->makePages($node);
  }

  private function makePages(Node $node): void {
    $pages = [];
    while (true) {
      $key = $node->data->pageName();
      $pages[$key] = [];

      if ($node->canBack()) {
        $prevNode = $node->prev;
        $pages[$key]["prev"] = $prevNode->data->pageName();
      }

      if ($node->canNext()) {
        $node = $node->next;

        $pages[$key]["next"] = $node->data->pageName();
        continue;
      }

      break;
    }

    $this->pages = $pages;
  }

  private function getPage(string $key): array {
    return isset($this->pages[$key]) ? $this->pages[$key] : [];
  }

  public function get(): string {
    $currPageKey = $this->request->cookies->get(self::CurrPageKey) ?? "";

    if ($currPageKey == "") {
      return "";
    }

    $curPage = $this->getPage($currPageKey);

    if ($this->request->onSubmitted() && isset($curPage["next"])) {
      $currPageKey = $curPage["next"];
    }

    if ($this->request->onBack() && isset($curPage["prev"]) ) {
      $currPageKey = $curPage["prev"];
    }

    return $currPageKey;
  }

  private function makeCookie(string $key, string $value, string $ttl = ""): Cookie {

    if ($ttl == "") {
      $now = time();
      $ttl = strtotime('+1 day', $now);
    }

    return Cookie::create($key)
      ->withValue($value)
      ->withExpires($ttl)
      ->withDomain($this->request->getBaseUrl())
      ->withSecure(true);
  }

  public function store(string $value): void {
    $this->response->headers->setCookie(
      $this->makeCookie(self::CurrPageKey, $value)
    );

    $this->response->sendHeaders();
  }

  public function flush(): void {
    $this->response->headers->removeCookie(
      self::CurrPageKey,
      "/",
      $this->request->getBaseUrl(),
    );

    $this->response->sendHeaders();
  }
}