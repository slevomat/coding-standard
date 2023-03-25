## Type hints

#### SlevomatCodingStandard.TypeHints.DeclareStrictTypes 🔧

Enforces having `declare(strict_types = 1)` at the top of each PHP file. Allows configuring how many newlines should be between the `<?php` opening tag and the `declare` statement.

Sniff provides the following settings:

* `declareOnFirstLine`: requires `declare` on the first line right after `<?php`
* `linesCountBeforeDeclare`: allows to set 0 to N lines to be between `declare` and previous statement. This option is ignored when `declareOnFirstLine` is enabled.
* `linesCountAfterDeclare`: allows to set 0 to N lines to be between `declare` and next statement
* `spacesCountAroundEqualsSign`: allows to set number of required spaces around the `=` operator

#### SlevomatCodingStandard.TypeHints.DisallowArrayTypeHintSyntax 🔧

Disallows usage of array type hint syntax (e.g. `int[]`, `bool[][]`) in phpDocs in favour of generic type hint syntax (eg. `array<int>`, `array<array<bool>>`).

Sniff provides the following settings:

* `traversableTypeHints`: helps fixer detect traversable type hints so `\Traversable|int[]` can be converted to `\Traversable<int>`.

#### SlevomatCodingStandard.TypeHints.DisallowMixedTypeHint

Disallows usage of "mixed" type hint in phpDocs.

#### SlevomatCodingStandard.TypeHints.LongTypeHints 🔧

Enforces using shorthand scalar typehint variants in phpDocs: `int` instead of `integer` and `bool` instead of `boolean`. This is for consistency with native scalar typehints which also allow shorthand variants only.

#### SlevomatCodingStandard.TypeHints.NullTypeHintOnLastPosition 🔧

Enforces `null` type hint on last position in annotations.

#### SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue 🔧🚧

Checks whether the nullablity `?` symbol is present before each nullable and optional parameter (which are marked as `= null`):

```php
function foo(
	int $foo = null, // ? missing
	?int $bar = null // correct
) {

}
```

#### SlevomatCodingStandard.TypeHints.ParameterTypeHint 🔧🚧

* Checks for missing parameter typehints in case they can be declared natively. If the phpDoc contains something that can be written as a native PHP 7.0+ typehint, this sniff reports that.
* Checks for useless `@param` annotations. If the native method declaration contains everything and the phpDoc does not add anything useful, it's reported as useless and can optionally be automatically removed with `phpcbf`.
* Forces to specify what's in traversable types like `array`, `iterable` and `\Traversable`.

Sniff provides the following settings:

* `enableObjectTypeHint`: enforces to transform `@param object` into native `object` typehint. It's on by default if you're on PHP 7.2+
* `enableMixedTypeHint`: enforces to transform `@param mixed` into native `mixed` typehint. It's on by default if you're on PHP 8.0+
* `enableUnionTypeHint`: enforces to transform `@param string|int` into native `string|int` typehint. It's on by default if you're on PHP 8.0+
* `enableIntersectionTypeHint`: enforces to transform `@param Foo&Bar` into native `Foo&Bar` typehint. It's on by default if you're on PHP 8.1+
* `enableStandaloneNullTrueFalseTypeHints`: enforces to transform `@param true`, `@param false` or `@param null` into native typehints. It's on by default if you're on PHP 8.2+
* `traversableTypeHints`: enforces which typehints must have specified contained type. E.g. if you set this to `\Doctrine\Common\Collections\Collection`, then `\Doctrine\Common\Collections\Collection` must always be supplied with the contained type: `\Doctrine\Common\Collections\Collection|Foo[]`.

This sniff can cause an error if you're overriding or implementing a parent method which does not have typehints. In such cases add `@phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint` annotation to the method to have this sniff skip it.

#### SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing 🔧

* Checks that there's a single space between a typehint and a parameter name: `Foo $foo`
* Checks that there's no whitespace between a nullability symbol and a typehint: `?Foo`

#### SlevomatCodingStandard.TypeHints.PropertyTypeHint 🔧🚧

* Checks for missing property typehints in case they can be declared natively. If the phpDoc contains something that can be written as a native PHP 7.4+ typehint, this sniff reports that.
* Checks for useless `@var` annotations. If the native method declaration contains everything and the phpDoc does not add anything useful, it's reported as useless and can optionally be automatically removed with `phpcbf`.
* Forces to specify what's in traversable types like `array`, `iterable` and `\Traversable`.

Sniff provides the following settings:

* `enableNativeTypeHint`: enforces to transform `@var int` into native `int` typehint. It's on by default if you're on PHP 7.4+
* `enableMixedTypeHint`: enforces to transform `@var mixed` into native `mixed` typehint. It's on by default if you're on PHP 8.0+. It can be enabled only when `enableNativeTypeHint` is enabled too.
* `enableUnionTypeHint`: enforces to transform `@var string|int` into native `string|int` typehint. It's on by default if you're on PHP 8.0+. It can be enabled only when `enableNativeTypeHint` is enabled too.
* `enableIntersectionTypeHint`: enforces to transform `@var Foo&Bar` into native `Foo&Bar` typehint. It's on by default if you're on PHP 8.1+. It can be enabled only when `enableNativeTypeHint` is enabled too.
* `enableStandaloneNullTrueFalseTypeHints`: enforces to transform `@var true`, `@var false` or `@var null` into native typehints. It's on by default if you're on PHP 8.2+. It can be enabled only when `enableNativeTypeHint` is enabled too.
* `traversableTypeHints`: enforces which typehints must have specified contained type. E.g. if you set this to `\Doctrine\Common\Collections\Collection`, then `\Doctrine\Common\Collections\Collection` must always be supplied with the contained type: `\Doctrine\Common\Collections\Collection|Foo[]`.

This sniff can cause an error if you're overriding parent property which does not have typehints. In such cases add `@phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint` annotation to the property to have this sniff skip it.

#### SlevomatCodingStandard.TypeHints.ReturnTypeHint 🔧🚧

* Checks for missing return typehints in case they can be declared natively. If the phpDoc contains something that can be written as a native PHP 7.0+ typehint, this sniff reports that.
* Checks for useless `@return` annotations. If the native method declaration contains everything and the phpDoc does not add anything useful, it's reported as useless and can optionally be automatically removed with `phpcbf`.
* Forces to specify what's in traversable types like `array`, `iterable` and `\Traversable`.

Sniff provides the following settings:

* `enableObjectTypeHint`: enforces to transform `@return object` into native `object` typehint. It's on by default if you're on PHP 7.2+
* `enableStaticTypeHint`: enforces to transform `@return static` into native `static` typehint. It's on by default if you're on PHP 8.0+
* `enableMixedTypeHint`: enforces to transform `@return mixed` into native `mixed` typehint. It's on by default if you're on PHP 8.0+
* `enableUnionTypeHint`: enforces to transform `@return string|int` into native `string|int` typehint. It's on by default if you're on PHP 8.0+.
* `enableIntersectionTypeHint`: enforces to transform `@return Foo&Bar` into native `Foo&Bar` typehint. It's on by default if you're on PHP 8.1+.
* `enableNeverTypeHint`: enforces to transform `@return never` into native `never` typehint. It's on by default if you're on PHP 8.1+.
* `enableStandaloneNullTrueFalseTypeHints`: enforces to transform `@return true`, `@return false` or `@return null` into native typehints. It's on by default if you're on PHP 8.2+.
* `traversableTypeHints`: enforces which typehints must have specified contained type. E.g. if you set this to `\Doctrine\Common\Collections\Collection`, then `\Doctrine\Common\Collections\Collection` must always be supplied with the contained type: `\Doctrine\Common\Collections\Collection|Foo[]`.

This sniff can cause an error if you're overriding or implementing a parent method which does not have typehints. In such cases add `@phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint` annotation to the method to have this sniff skip it.

#### SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing 🔧

Enforces consistent formatting of return typehints, like this:

```php
function foo(): ?int
```

Sniff provides the following settings:

* `spacesCountBeforeColon`: the number of spaces expected between closing brace and colon.

#### SlevomatCodingStandard.TypeHints.UnionTypeHintFormat 🔧

Checks format of union type hints.

Sniff provides the following settings:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 8.0 or higher.
* `withSpaces`: `yes` requires spaces around `|`, `no` requires no space around `|`. None is set by default so both are enabled.
* `shortNullable`: `yes` requires usage of `?` for nullable type hint, `no` disallows it. None is set by default so both are enabled.
* `nullPosition`: `first` requires `null` on first position in the type hint, `last` requires last position. None is set by default so `null` can be everywhere.

#### SlevomatCodingStandard.TypeHints.UselessConstantTypeHint 🔧

Reports useless `@var` annotation (or whole documentation comment) for constants because the type of constant is always clear.
