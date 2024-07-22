<?php

use Gargantua\Contract\Page;
use Gargantua\Contract\CanNavigateBack;
use Gargantua\Contract\CanLabel;
use Gargantua\Http\Request;


function preEcho(array $out) {
  echo "<code>";
  echo "<pre>";
  print_r($out);
  echo "</pre>";
  echo "</code>";
}

class SignUp implements Page, CanNavigateBack, CanLabel {
  public function pageName(): string {
    return "signUp";
  }

  public function onSubmit(Request $request): void {}

  public function onNext(array $payload): void {}

  public function onCompleted(): void {}

  public function onNavigateBack(): void {}

  public function label(): string {
    return "Welcome to Onboarding";
  }

  public function icon(): string {
    return "";
  }
}


class Otp implements Page, CanLabel {
  public function pageName(): string {
    return "Otp";
  }

  public function onSubmit(Request $request): void {}

  public function onNext(array $payload): void {}

  public function onCompleted(): void {}

  public function onNavigateBack(): void {}

  public function label(): string {
    return "Please verify your onboarding";
  }

  public function icon(): string {
    return "";
  }
}