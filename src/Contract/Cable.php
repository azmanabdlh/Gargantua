<?php

namespace Gargantua\Contract;

use Closure;

interface Cable {
  function handle(array $payload, Closure $next): void;
}
