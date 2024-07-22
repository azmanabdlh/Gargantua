<?php

namespace Gargantua\Contract;

use Gargantua\Http\Request;

interface Cable {
  function handle(Request $request): void;
}
