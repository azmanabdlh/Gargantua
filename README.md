
## Gargantua 🪐
Form wizard can be challenging and may require a higher effort to complete them. Gargantua is a minimalist form wizard library.

![demo](/demo.gif)

#### Table of Content
* [Installation](#installation)
* [Quick Start](#quick-start)
* [API](#api)
  * [Form](#form)
  * [Page](#page)
  * [PageUtils](#pageutils)
  * [Cable](#cable)





## Installation
Before you install, it's important to check that your PHP version should be `PHP 7.3+`.
```bash
composer require azmanabdlh/gargantua
```

## Quick Start
![quick-start](/quick-demo.png)

## API

### Form
```php
use Gargantua\Contract\Cache;
use Gargantua\Contract\Page;
use Gargantua\Contract\Request;


public static function provide(
  Page[] $page,
  Cache $cache,
  Request $request
): Form
```

```php
// get current page utils.
public function page(): PageUtils
```

```php
// check form page can be navigate back.
public function canBack(): bool
```

```php
// check form page is last.
public function last(): bool
```


### Page
Page provide a form page

```php
// provide form page name.
public function pageName(): string
```

```php
// when form page submitted try to invoke func.
public function onSubmit(Request $request): void
```

```php
// try to move the next page can invoke func.
public function onNext(array $payload): void
```

```php
// when it reaches the tail, each form page invoke func.
public function onCompleted(): void
```

```php
use Gargantua\Contract\CanNavigateBack;

// try back to the prev page can invoke func.
public function onNavigateBack(): void
```


### PageUtils
PageUtils provide Page utilities

```php
// validate page is active.
public function is(string $pageName): bool
```

```php
// get page label.
public function label(): string
```

```php
// get page icon.
public function icon(): string


// example
public function icon(): string {
  return "<img .../>"; // or <i icon="user-account"></i>
}
```
```php
// get current page number.
public function number(): int
```


### Cable
Intercept form page using Cable for each Page submitted.

```php

$form->capture([
  new VerifyJwtToken()
]);


// handle logic.
function handle(Request $request, Closure $next): void
```


## License
Licensed under [MIT](http://www.opensource.org/licenses/mit-license.php). Totally free for private or commercial projects, or any other uses.