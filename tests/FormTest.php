<?php


use Gargantua\Form;
use Gargantua\PageLinked;
use Gargantua\Contract\Page;
use Gargantua\Contract\CanNavigateBack;
use Gargantua\Contract\Session;
use Gargantua\Support\PageUtils;
use Gargantua\Http\Request;


function mockPage() : array {
  $signUp = new class implements Page, CanNavigateBack {
    public function pageName(): string {
      return "signUp";
    }

    public function onSubmit(Request $request): void {}

    public function onNext(array $payload): void {}

    public function onCompleted(): void {}

    public function onNavigateBack(): void {
      echo $this->pageName() .": navigate back";
    }

  };
  $onboarding =  new class implements Page {
    public function pageName(): string {
      return "onboarding";
    }

    public function onSubmit(Request $request): void {}

    public function onNext(array $payload): void {}

    public function onCompleted(): void {}
  };

  return [$signUp, $onboarding];
}


test("build instance form", function() {
  $pages = mockPage();
  $session = Mockery::mock(Session::class);

  $form = Form::provide(
    $pages,
    $session
  );
  expect($form)->toBeInstanceOf(Form::class);
});


test("get form page", function() {
  [$signUp, $onboarding] = mockPage();
  $session = Mockery::mock(Session::class);

  $form = Form::provide(
    [$signUp, $onboarding],
    $session
  );

  $session->shouldReceive('get')->once()->andReturn("");
  expect($form->page())->toEqual(new PageUtils(1, $signUp));


  $session->shouldReceive('get')->once()->andReturn($onboarding->pageName());
  expect($form->page())->toEqual(new PageUtils(2, $onboarding));
});


test("get form page can be back", function() {
  [$signUp, $onboarding] = mockPage();
  $session = Mockery::mock(Session::class);

  $form = Form::provide(
    [$signUp, $onboarding],
    $session,
  );

  $session->shouldReceive('get')->andReturn($onboarding->pageName());
  expect($form->canBack())->toBeTrue();
});


test("get last form page", function() {
  [$signUp, $onboarding] = mockPage();
  $session = Mockery::mock(Session::class);

  $form = Form::provide(
    [$signUp, $onboarding],
    $session,
  );

  $session->shouldReceive('get')->andReturn($onboarding->pageName());
  expect($form->last())->toBeTrue();
});
