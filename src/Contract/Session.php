<?php

namespace Gargantua\Contract;


interface Session {
  function store(string $value): void;
  function get(): string;
  function flush(): void;
}


