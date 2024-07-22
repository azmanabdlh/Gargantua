<?php

namespace Gargantua\Contract;

interface Page {
  function pageName(): string;
  function onSubmit(array $data): void;
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

