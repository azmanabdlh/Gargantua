<?php


namespace Gargantua\Cache;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie as CookieUtils;
use Symfony\Component\HttpFoundation\Request;

use Gargantua\Contract\Cache;


class Cookie implements Cache {

  public function __construct(
    private string $namespace = "",
  ) {
    $this->response = new Response();
    $this->request = Request::createFromGlobals();
  }

  public function get(string $key): string {
    return $this->request->cookies->get($key) || "";
  }

  private function make(string $key, string $value, string $ttl = ""): CookieUtils {

    if ($ttl == "") {
      $now = time();
      $ttl = strtotime('+1 day', $now);
    }

    return CookieUtils::create($this->namespace . ":". $key)
      ->withValue($value)
      ->withExpires($ttl)
      ->withDomain($this->request->getBaseUrl())
      ->withSecure(true);
  }

  public function store(string $key, string $value): void {
    $this->response->headers->setCookie(
      $this->make($key, $value)
    );
  }

  public function flush(): void {
    $this->response->headers->removeCookie(
      "pageName",
      "/",
      $this->request->getBaseUrl(),
    );
  }
}