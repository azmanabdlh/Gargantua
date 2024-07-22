<?php

namespace Gargantua\Contract;

use Gargantua\Http\Request;

interface Page {

  function pageName(): string;

  function onSubmit(Request $request): void;

  function onCompleted(): void;

  function onNext(array $data): void;

}

interface CanNavigateBack {

  function onNavigateBack(): void;

}

interface CanLabel {

  function icon(): string;

  function label(): string;

}

