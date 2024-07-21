<?php

namespace Gargantua\Contract;

interface Request {
  function get(string $key): string;
  function all(): array;
}


