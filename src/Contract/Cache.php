<?php

namespace Gargantua\Contract;

interface Cache {
  function store(string $key, string $value): void;
  function get(string $key): mixed;
  function flush(): void;
}


