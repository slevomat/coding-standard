## Exceptions

#### SlevomatCodingStandard.Exceptions.DeadCatch

This sniff finds unreachable catch blocks:

```php
try {
	doStuff();
} catch (\Throwable $e) {
	log($e);
} catch (\InvalidArgumentException $e) {
	// unreachable!
}
```

#### SlevomatCodingStandard.Exceptions.DisallowNonCapturingCatch

This sniff forbids use of non-capturing catch introduced in PHP 8.0 [PHP RFC: non-capturing catches](https://wiki.php.net/rfc/non-capturing_catches).

#### SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly 🔧🚧

In PHP 7.0, a [`Throwable` interface was added](https://wiki.php.net/rfc/throwable-interface) that allows catching and handling errors in more cases than `Exception` previously allowed. So, if the catch statement contained `Exception` on PHP 5.x, it means it should probably be rewritten to reference `Throwable` on PHP 7.x. This sniff enforces that.

#### SlevomatCodingStandard.Exceptions.RequireNonCapturingCatch 🔧

Sniff provides the following settings:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 8.0 or higher.

It requires non-capturing catch when the variable with exception is not used.
