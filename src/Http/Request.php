<?php

namespace Gargantua\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Gargantua\Event;

class Request extends SymfonyRequest {

  public function onSubmitted(): bool {
    return $this->isMethod("POST") && $this->request->has(Event::RequestSubmit);
  }

  public function onBack(): bool {
    return $this->isMethod("POST") && $this->request->has(Event::RequestBack);
  }
}