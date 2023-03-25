## Namespaces

#### SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses 🔧

Sniff checks whether `use` declarations at the top of a file are alphabetically sorted. Follows natural sorting and takes edge cases with special symbols into consideration. The following code snippet is an example of correctly sorted uses:

```php
use LogableTrait;
use LogAware;
use LogFactory;
use LoggerInterface;
use LogLevel;
use LogStandard;
```

Sniff provides the following settings:


* `psr12Compatible` (defaults to `true`): sets the required order to `classes`, `functions` and `constants`. `false` sets the required order to `classes`, `constants` and `functions`.
* `caseSensitive`: compare namespaces case sensitively, which makes this order correct:

```php
use LogAware;
use LogFactory;
use LogLevel;
use LogStandard;
use LogableTrait;
use LoggerInterface;
```

#### SlevomatCodingStandard.Namespaces.DisallowGroupUse

[Group use declarations](https://wiki.php.net/rfc/group_use_declarations) are ugly, make diffs ugly and this sniff prohibits them.

#### SlevomatCodingStandard.Namespaces.FullyQualifiedExceptions 🔧

This sniff reduces confusion in the following code snippet:

```php
try {
	$this->foo();
} catch (Exception $e) {
	// Is this the general exception all exceptions must extend from? Or Exception from the current namespace?
}
```

All references to types named `Exception` or ending with `Exception` must be referenced via a fully qualified name:

```php
try {
	$this->foo();
} catch (\FooCurrentNamespace\Exception $e) {

} catch (\Exception $e) {

}
```

Sniff provides the following settings:

* Exceptions with different names can be configured in `specialExceptionNames` property.
* If your codebase uses classes that look like exceptions (because they have `Exception` or `Error` suffixes) but aren't, you can add them to `ignoredNames` property and the sniff won't enforce them to be fully qualified. Classes with `Error` suffix have to be added to ignored only if they are in the root namespace (like `LibXMLError`).

#### SlevomatCodingStandard.Namespaces.FullyQualifiedGlobalConstants 🔧

All references to global constants must be referenced via a fully qualified name.

Sniff provides the following settings:

* `include`: list of global constants that must be referenced via FQN. If not set all constants are considered.
* `exclude`: list of global constants that are allowed not to be referenced via FQN.

#### SlevomatCodingStandard.Namespaces.FullyQualifiedGlobalFunctions 🔧

All references to global functions must be referenced via a fully qualified name.

Sniff provides the following settings:

* `include`: list of global functions that must be referenced via FQN. If not set all functions are considered.
* `includeSpecialFunctions`: include complete list of PHP internal functions that could be optimized when referenced via FQN.
* `exclude`: list of global functions that are allowed not to be referenced via FQN.

#### SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation 🔧

Enforces fully qualified names of classes and interfaces in phpDocs - in annotations. This results in unambiguous phpDocs.

#### SlevomatCodingStandard.Namespaces.MultipleUsesPerLine

Prohibits multiple uses separated by commas:

```php
use Foo, Bar;
```

#### SlevomatCodingStandard.Namespaces.NamespaceDeclaration 🔧

Enforces one space after `namespace`, disallows content between namespace name and semicolon and disallows use of bracketed syntax.

#### SlevomatCodingStandard.Namespaces.NamespaceSpacing 🔧

Enforces configurable number of lines before and after `namespace`.

Sniff provides the following settings:

* `linesCountBeforeNamespace`: allows to configure the number of lines before `namespace`.
* `linesCountAfterNamespace`: allows to configure the number of lines after `namespace`.

#### SlevomatCodingStandard.Namespaces.RequireOneNamespaceInFile

Requires only one namespace in a file.

#### SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly 🔧

Sniff provides the following settings:

* `searchAnnotations` (defaults to `false`): enables searching for mentions in annotations.
* `namespacesRequiredToUse`: if not set, all namespaces are required to be used. When set, only mentioned namespaces are required to be used. Useful in tandem with UseOnlyWhitelistedNamespaces sniff.
* `allowFullyQualifiedExceptions`, `specialExceptionNames` & `ignoredNames`: allows fully qualified exceptions. Useful in tandem with FullyQualifiedExceptions sniff.
* `allowFullyQualifiedNameForCollidingClasses`: allow fully qualified name for a class with a colliding use statement.
* `allowFullyQualifiedNameForCollidingFunctions`: allow fully qualified name for a function with a colliding use statement.
* `allowFullyQualifiedNameForCollidingConstants`: allow fully qualified name for a constant with a colliding use statement.
* `allowFullyQualifiedGlobalClasses`: allows using fully qualified classes from global space (i.e. `\DateTimeImmutable`).
* `allowFullyQualifiedGlobalFunctions`: allows using fully qualified functions from global space (i.e. `\phpversion()`).
* `allowFullyQualifiedGlobalConstants`: allows using fully qualified constants from global space (i.e. `\PHP_VERSION`).
* `allowFallbackGlobalFunctions`: allows using global functions via fallback name without `use` (i.e. `phpversion()`).
* `allowFallbackGlobalConstants`: allows using global constants via fallback name without `use` (i.e. `PHP_VERSION`).
* `allowPartialUses`: allows using and referencing whole namespaces.

#### SlevomatCodingStandard.Namespaces.UseFromSameNamespace 🔧

Sniff prohibits uses from the same namespace:

```php
namespace Foo;

use Foo\Bar;
```

#### SlevomatCodingStandard.Namespaces.UseDoesNotStartWithBackslash 🔧

Disallows leading backslash in use statement:

```php
use \Foo\Bar;
```

#### SlevomatCodingStandard.Namespaces.UseSpacing 🔧

Enforces configurable number of lines before first `use`, after last `use` and between two different types of `use` (eg. between `use function` and `use const`). Also enforces zero number of lines between same types of `use`.

Sniff provides the following settings:

* `linesCountBeforeFirstUse`: allows to configure the number of lines before first `use`.
* `linesCountBetweenUseTypes`: allows to configure the number of lines between two different types of `use`.
* `linesCountAfterLastUse`: allows to configure the number of lines after last `use`.

#### SlevomatCodingStandard.Namespaces.UseOnlyWhitelistedNamespaces

Sniff disallows uses of other than configured namespaces.

Sniff provides the following settings:

* `namespacesRequiredToUse`: namespaces in this array are the only ones allowed to be used. E.g. root project namespace.
* `allowUseFromRootNamespace`: also allow using top-level namespace:

```php
use DateTimeImmutable;
```

#### SlevomatCodingStandard.Namespaces.UselessAlias 🔧

Looks for `use` alias that is same as unqualified name.

#### SlevomatCodingStandard.Namespaces.UnusedUses 🔧

Looks for unused imports from other namespaces.

Sniff provides the following settings:

* `searchAnnotations` (defaults to `false`): enables searching for class names in annotations.
* `ignoredAnnotationNames`: case-sensitive list of annotation names that the sniff should ignore (only the name is ignored, annotation content is still searched). Useful for name collisions like `@testCase` annotation and `TestCase` class.
* `ignoredAnnotations`: case-sensitive list of annotation names that the sniff ignore completely (both name and content are ignored). Useful for name collisions like `@group Cache` annotation and `Cache` class.
