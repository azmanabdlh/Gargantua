<?php

namespace Gargantua\Contract;

interface Request {
  function get(string $key, mixed $default = null): mixed;
  function toArray(): array;
}


