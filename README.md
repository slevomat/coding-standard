# Slevomat Coding Standard

[![Latest version](https://img.shields.io/packagist/v/slevomat/coding-standard.svg?style=flat-square&colorB=007EC6)](https://packagist.org/packages/slevomat/coding-standard)
[![Downloads](https://img.shields.io/packagist/dt/slevomat/coding-standard.svg?style=flat-square&colorB=007EC6)](https://packagist.org/packages/slevomat/coding-standard)
[![Travis build status](https://img.shields.io/travis/slevomat/coding-standard/master.svg?label=travis&style=flat-square)](https://travis-ci.org/slevomat/coding-standard)
[![AppVeyor build status](https://img.shields.io/appveyor/ci/slevomat/coding-standard/master.svg?label=appveyor&style=flat-square)](https://ci.appveyor.com/project/slevomat/coding-standard)
[![Code coverage](https://img.shields.io/coveralls/slevomat/coding-standard/master.svg?style=flat-square)](https://coveralls.io/github/slevomat/coding-standard?branch=master)
![PHPStan](https://img.shields.io/badge/style-level%205-brightgreen.svg?style=flat-square&label=phpstan)

Slevomat Coding Standard for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) complements [Consistence Coding Standard](https://github.com/consistence/coding-standard) by providing sniffs with additional checks.

## Table of contents

1. [Sniffs included in this standard](#sniffs-included-in-this-standard)
  - [Functional - improving the safety and behaviour of code](#functional---improving-the-safety-and-behaviour-of-code)
  - [Cleaning - detecting dead code](#cleaning---detecting-dead-code)
  - [Formatting - rules for consistent code looks](#formatting---rules-for-consistent-code-looks)
2. [Installation](#installation)
3. [Using the standard as a whole](#using-the-standard-as-a-whole)
4. [Using individual sniffs](#using-individual-sniffs)
5. [Fixing errors automatically](#fixing-errors-automatically)
6. [Suppressing sniffs locally](#suppressing-sniffs-locally)
7. [Contributing](#contributing)

## Sniffs included in this standard

🔧 = [Automatic errors fixing](#fixing-errors-automatically)

🚧 = [Sniff check can be suppressed locally](#suppressing-sniffs-locally)

### Functional - improving the safety and behaviour of code

#### SlevomatCodingStandard.TypeHints.TypeHintDeclaration 🔧🚧

* Checks for missing property types in phpDoc `@var`.
* Checks for missing typehints in case they can be declared natively. If the phpDoc contains something that can be written as a native PHP 7.0 or 7.1 typehint, this sniff reports that.
* Checks for missing `@return` and/or native return typehint in case the method body contains `return` with a value.
* Checks for useless doc comments. If the native method declaration contains everything and the phpDoc does not add anything useful, it's reported as useless and can optionally be automatically removed with `phpcbf`.
* Some phpDocs might still be useful even if they do not add any typehint information. They can contain textual descriptions of code elements and also some meaningful annotations like `@expectException` or `@dataProvider`.
* Forces to specify what's in traversable types like `array`, `iterable` and `\Traversable`.
* Distinguishes what's possible in PHP 7.0 (scalar typehints) and PHP 7.1 (nullable types and void return typehint).

Sniff provides the following settings:

* `enableNullableTypeHints`: enforces to transform `Foo|null` in phpDoc into `?Foo` in native typehint. It's on by default if you're on PHP 7.1.
* `enableVoidTypeHint`: enforces to transform `@return void` into native `void` return typehint. It's on by default if you're on PHP 7.1.
* `traversableTypeHints`: enforces which typehints must have specified contained type. E. g. if you set this to `\Doctrine\Common\Collections\Collection`, then `\Doctrine\Common\Collections\Collection` must always be supplied with the contained type: `\Doctrine\Common\Collections\Collection|Foo[]`.
* `usefulAnnotations`: prevents reporting and removing useless phpDocs if they contain an additional configured annotation like `@dataProvider`.
* `enableEachParameterAndReturnInspection`: enables inspection and fixing of `@param` and `@return` annotations separately. Useful when you only want to document parameters or return values that could not be expressed natively (i.e. member types of `array` or `Traversable`).

#### SlevomatCodingStandard.Exceptions.ReferenceThrowableOnly 🔧

In PHP 7.0, a [`Throwable` interface was added](https://wiki.php.net/rfc/throwable-interface) that allows catching and handling errors in more cases than `Exception` previously allowed. So if the catch statement contained `Exception` on PHP 5.x, it means it should probably be rewritten to reference `Throwable` on PHP 7.x. This sniff enforces that.

#### SlevomatCodingStandard.TypeHints.DeclareStrictTypes 🔧

Enforces having `declare(strict_types = 1)` at the top of each PHP file. Allows configuring how many newlines should be between the `<?php` opening tag and the `declare` statement.

Sniff provides the following settings:

* `newlinesCountBetweenOpenTagAndDeclare`: allows to set 0 to N newlines to be between `<?php` and `declare`
* `spacesCountAroundEqualsSign`: allows to set number of required spaces around the `=` operator

#### SlevomatCodingStandard.ControlStructures.AssignmentInCondition

Disallows assignments in `if`, `elseif` and `do-while` loop conditions:

```php
if ($file = findFile($path)) {

}
```

Assignment in `while` loop condition is specifically allowed because it's commonly used.

This is a great addition to already existing `SlevomatCodingStandard.ControlStructures.YodaComparison` because it prevents the danger of assigning something by mistake instead of using comparison operator like `===`.

#### SlevomatCodingStandard.ControlStructures.DisallowEqualOperators 🔧

Disallows using loose `==` and `!=` comparison operators. Use `===` and `!==` instead, they are much more secure and predictable.

### Cleaning - detecting dead code

#### SlevomatCodingStandard.Classes.UnusedPrivateElements 🚧

Although PHP_CodeSniffer is not suitable for static analysis because it is limited to analysing one file at a time, it is possible to use it to perform certain checks. `UnusedPrivateElementsSniff` checks for unused methods, unused or write-only properties in a class and unused private constants. Reported unused elements are safe to remove.

This is very useful during refactoring to clean up dead code and injected dependencies.

Sniff provides the following settings:

* `alwaysUsedPropertiesAnnotations`: mark certain properties as always used, for example the ones with `@ORM\Column`
* `alwaysUsedPropertiesSuffixes`: mark properties with name ending with a certain string to be always marked as used

#### SlevomatCodingStandard.Namespaces.UnusedUses 🔧

Looks for unused imports from other namespaces.

Sniff provides the following settings:

* `searchAnnotations` (defaults to `false`): enables searching for mentions in annotations, which is especially useful for projects using [Doctrine Annotations](https://github.com/doctrine/annotations)

#### SlevomatCodingStandard.Namespaces.UseFromSameNamespace

Prohibits uses from the same namespace:

```php
namespace Foo;

use Foo\Bar;
```

#### SlevomatCodingStandard.Exceptions.DeadCatch

This sniffs finds unreachable catch blocks:

```php
try {
	doStuff();
} catch (\Throwable $e) {
	log($e);
} catch (\InvalidArgumentException $e) {
	// unreachable!
}
```

### Formatting - rules for consistent code looks

#### SlevomatCodingStandard.Arrays.TrailingArrayComma 🔧

Commas after last element in an array make adding a new element easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line arrays and requires short array syntax `[]`.

#### SlevomatCodingStandard.ControlStructures.YodaComparison 🔧

[Yoda conditions](https://en.wikipedia.org/wiki/Yoda_conditions) decrease code comprehensibility and readability by switching operands around comparison operators forcing the reader to read the code in an unnatural way.

`YodaComparisonSniff` looks for and fixes such comparisons not only in `if` statements but in the whole code.

#### SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses 🔧

Checks whether uses at the top of a file are alphabetically sorted. Follows natural sorting and takes edge cases with special symbols into consideration. The following code snippet is an example of correctly sorted uses:

```php
use Baz;
use Foo;
use Foo\Bar;
use Foo_Baz;
use Foo_bar;
use Foo1;
use Foo2;
use Foo11;
use Foo22;
use FooBaz;
use Foobar;
use Foobarz;
```

#### SlevomatCodingStandard.TypeHints.LongTypeHints

Enforces using shorthand scalar typehint variants in phpDocs: `int` instead of `integer` and `bool` instead of `boolean`. This is for consistency with native scalar typehints which also allow shorthand variants only.

#### SlevomatCodingStandard.Files.TypeNameMatchesFileName

For projects not following the [PSR-0](http://www.php-fig.org/psr/psr-0/) or [PSR-4](http://www.php-fig.org/psr/psr-4/) autoloading standards, this sniff checks whether a namespace and a name of a class/interface/trait follows agreed-on way to organize code into directories and files.

Other than enforcing that the type name must match the name of the file it's contained in, this sniff is very configurable. Consider the following sample configuration:

```xml
<rule ref="SlevomatCodingStandard.Files.TypeNameMatchesFileName">
	<properties>
		<property name="rootNamespaces" type="array" value="app/ui=>Slevomat\UI,app=>Slevomat,build/SlevomatSniffs/Sniffs=>SlevomatSniffs\Sniffs,tests/ui=>Slevomat\UI,tests=>Slevomat"/>
		<property name="skipDirs" type="array" value="components,forms,model,models,services,stubs,data,new"/>
		<property name="ignoredNamespaces" type="array" value="Slevomat\Services"/>
	</properties>
</rule>
```

Sniff provides the following settings:

* `rootNamespaces` property expects configuration similar to PSR-4 - project directories mapped to certain namespaces.
* `skipDirs` are not taken into consideration when comparing a path to a namespace. For example, with the above settings, file at path `app/services/Product/Product.php` is expected to contain `Slevomat\Product\Product`, not `Slevomat\services\Product\Product`.
* `extensions` - allow different file extensions. Default is `php`.
* `ignoredNamespaces` - sniff is not performed on these namespaces

#### SlevomatCodingStandard.Classes.ClassConstantVisibility

In PHP 7.1 it's possible to declare [visibility of class constants](https://wiki.php.net/rfc/class_const_visibility). In a similar vein to optional declaration of visibility for properties and methods which is actually required in sane coding standards, this sniff also requires to declare visibility for all class constants.

```php
const FOO = 1; // visibility missing!
public const BAR = 2; // correct
```

#### SlevomatCodingStandard.TypeHints.ReturnTypeHintSpacing 🔧

Enforces consistent formatting of return typehints, like this:

```php
function foo(): ?int
```

Sniff provides the following settings:

* `spacesCountBeforeColon` - the number of spaces expected between closing brace and colon

#### SlevomatCodingStandard.TypeHints.NullableTypeForNullDefaultValue 🔧

Checks whether the nullablity `?` symbol is present before each nullable and optional parameter (which are marked as `= null`):

```php
function foo(
	int $foo = null, // ? missing
	?int $bar = null // correct
) {

}
```

#### SlevomatCodingStandard.TypeHints.ParameterTypeHintSpacing 🔧

* Checks that there's a single space between a typehint and a parameter name: `Foo $foo`
* Checks that there's no whitespace between a nullability symbol and a typehint: `?Foo`

#### SlevomatCodingStandard.Namespaces.DisallowGroupUse

[Group use declarations](https://wiki.php.net/rfc/group_use_declarations) are ugly, make diffs ugly and this sniffs prohibits them.

#### SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameAfterKeyword

Enforces fully qualified type references after configurable set of language keywords.

For example with the following setting, extended or implemented type must always be referenced with a fully qualified name:

```xml
<rule ref="SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameAfterKeyword">
	<properties>
		<property name="keywordsToCheck" type="array" value="T_EXTENDS,T_IMPLEMENTS"/>
	</properties>
</rule>
```

#### SlevomatCodingStandard.Namespaces.FullyQualifiedExceptions

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
* If your codebase uses classes that look like exceptions (because they have `Exception` or `Error` suffixes) but aren't, you can add them to `ignoredNames` property and the sniff won't enforce them to be fully qualified. Classes with `Error` suffix has to be added to ignored only if they are in the root namespace (like `LibXMLError`).

#### SlevomatCodingStandard.Namespaces.MultipleUsesPerLine

Prohibits multiple uses separated by commas:

```php
use Foo, Bar;
```

#### SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly 🔧

Enforces to use all referenced names with configurable omissions:

`fullyQualifiedKeywords` - allows fully qualified names after certain keywords. Useful in tandem with FullyQualifiedClassNameAfterKeyword sniff.

`allowFullyQualifiedExceptions`, `specialExceptionNames` & `ignoredNames` - allows fully qualified exceptions. Useful in tandem with FullyQualifiedExceptions sniff.

`allowPartialUses` - allows using and referencing whole namespaces:

```php
use Foo;
//...
new Foo\Bar();
```

`namespacesRequiredToUse` - if not set, all namespaces are required to be used. When set, only mentioned namespaces are required to be used. Useful in tandem with UseOnlyWhitelistedNamespaces sniff.

`allowFullyQualifiedNameForCollidingClasses` - allow fully qualified name for a class with a colliding use statement

`allowFullyQualifiedGlobalClasses` - allows using fully qualified names from global space (i.e. \DateTimeImmutable)

#### SlevomatCodingStandard.Namespaces.UseOnlyWhitelistedNamespaces

Disallows uses of other than configured namespaces.

Sniff provides the following settings:

* `namespacesRequiredToUse` - namespaces in this array are the only ones allowed to be used. E. g. root project namespace.
* `allowUseFromRootNamespace` - also allow using top-level namespace:

```php
use DateTimeImmutable;
```

#### SlevomatCodingStandard.Namespaces.UseDoesNotStartWithBackslash

Disallows leading backslash in use statement:

```php
use \Foo\Bar;
```

#### SlevomatCodingStandard.Types.EmptyLinesAroundTypeBraces

Enforces one empty line after opening class/interface/trait brace and one empty line before the closing brace.

#### SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameInAnnotation

Enforces fully qualified names of classes and interfaces in phpDocs - in `@var`, `@param`, `@return`, `@throws`. This results in unambiguous phpDocs.

#### SlevomatCodingStandard.Commenting.ForbiddenAnnotations 🔧

Reports forbidden annotations. No annotations are forbidden by default, the configuration is completely up to the user. It's recommended to forbid obsolete and inappropriate annotations like:

* `@author`, `@created`, `@version` - we have version control systems
* `@package` - we have namespaces
* `@copyright`, `@license` - it's not necessary to repeat licensing information in each file
* `@throws` - it's not possible to enforce this annotation and the information can become outdated

Sniff provides the following settings:

* `forbiddenAnnotations`: allows to configure which annotations are forbidden to be used
* `linesCountAfterOpeningBrace`: allows to configure the number of lines after opening brace
* `linesCountBeforeClosingBrace`: allows to configure the number of lines before closing brace

## Installation

The recommended way to install Slevomat Coding Standard is [through Composer](http://getcomposer.org).

```JSON
{
	"require-dev": {
		"slevomat/coding-standard": "^2.0"
	}
}
```

It's also recommended to install [jakub-onderka/php-parallel-lint](https://github.com/JakubOnderka/PHP-Parallel-Lint) which checks source code for syntax errors. Sniffs count on the processed code to be syntatically valid (no parse errors), otherwise they can behave unexpectedly. It is advised to run `PHP-Parallel-Lint` in your build tool before running `PHP_CodeSniffer` and exiting the build process early if `PHP-Parallel-Lint` fails.

## Using the standard as a whole

If you want to use the whole coding standard, besides requiring `slevomat/coding-standard` in composer.json, require also Consistence Coding Standard:

```JSON
{
	"require-dev": {
		"consistence/coding-standard": "^0.10"
	}
}
```

Then mention both standards in `ruleset.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<rule ref="vendor/consistence/coding-standard/Consistence/ruleset.xml" />
	<rule ref="vendor/slevomat/coding-standard/SlevomatCodingStandard/ruleset.xml" />
	<!-- additional settings -->
</ruleset>
```

To check your code base for violations, run `PHP-Parallel-Lint` and `PHP_CodeSniffer` from the command line:

```
vendor/bin/parallel-lint src tests
vendor/bin/phpcs --standard=ruleset.xml --extensions=php --encoding=utf-8 --tab-width=4 -sp src tests
```

## Using individual sniffs

If you don't want to follow the whole standard, but find a handful of included sniffs useful, you can use them selectively.

You can choose one of two ways to run only selected sniffs from the standard on your codebase:

### List all sniffs to run

Mention Slevomat Conding Standard in your project's `ruleset.xml`:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<rule ref="vendor/slevomat/coding-standard/SlevomatCodingStandard/ruleset.xml" />
</ruleset>
```

When running `phpcs` [on the command line](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Usage), use the `--sniffs` option to list all the sniffs you want to use separated by a comma:

```
vendor/bin/phpcs --standard=ruleset.xml \
--sniffs=SlevomatCodingStandard.ControlStructures.YodaComparison,SlevomatCodingStandard.Namespaces.AlphabeticallySortedUses \
--extensions=php --encoding=utf-8 --tab-width=4 -sp src tests
```

### Use all sniffs except for the unwanted ones

Mention Slevomat Conding Standard in your project's `ruleset.xml` and list all the excluded sniffs:

```xml
<?xml version="1.0"?>
<ruleset name="AcmeProject">
	<rule ref="vendor/slevomat/coding-standard/SlevomatCodingStandard/ruleset.xml">
		<exclude name="SlevomatCodingStandard.Namespaces.FullyQualifiedClassNameAfterKeyword"/>
		<exclude name="SlevomatCodingStandard.Namespaces.FullyQualifiedExceptions"/>
		<exclude name="SlevomatCodingStandard.Namespaces.ReferenceUsedNamesOnly"/>
		<exclude name="SlevomatCodingStandard.Namespaces.UseOnlyWhitelistedNamespaces"/>
		<exclude name="SlevomatCodingStandard.Types.EmptyLinesAroundTypeBraces"/>
		<exclude name="SlevomatCodingStandard.Files.TypeNameMatchesFileName"/>
	</rule>
</ruleset>
```

Then run the remaining sniffs in the usual way:

```
vendor/bin/phpcs --standard=ruleset.xml --extensions=php --encoding=utf-8 --tab-width=4 -sp src tests
```

## Fixing errors automatically

Sniffs in this standard marked by the 🔧 symbol support [automatic fixing of coding standard violations](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically). To fix your code automatically, run phpcbf insteand of phpcs:

```
vendor/bin/phpcbf --standard=ruleset.xml --extensions=php --encoding=utf-8 --tab-width=4 -sp src tests
```

Always remember to back up your code before performing automatic fixes and check the results with your own eyes as the automatic fixer can sometimes produce unwanted results.

## Suppressing sniffs locally

Selected sniffs in this standard marked by the 🚧 symbol can be suppressed for a specific piece of code using an annotation. Consider the following example:

```php
/**
 * @param int $max
 */
public function createProgressBar($max = 0): ProgressBar
{

}
```

The parameter `$max` could have a native `int` scalar typehint. But because the method in the parent class does not have this typehint, so this one cannot have it either. PHP_CodeSniffer shows a following error:

```
----------------------------------------------------------------------
FOUND 1 ERROR AFFECTING 1 LINE
----------------------------------------------------------------------
 67 | ERROR | [x] Method ErrorsConsoleStyle::createProgressBar()
    |       |     does not have parameter type hint for its parameter $max
    |       |     but it should be possible to add it based on @param
    |       |     annotation "int".
    |       |     (SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint)
```

If we want to suppress this error instead of fixing it, we can take the error code (`SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint`) and use it with a `@phpcsSuppress` annotation like this:

```php
/**
 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
 * @param int $max
 */
public function createProgressBar($max = 0): ProgressBar
{

}
```

## Contributing

To make this repository work on your machine, clone it and run these two commands in the root directory of the repository:

```
composer install
bin/phing
```

After writing some code and editing or adding unit tests, run phing again to check that everything is OK:

```
bin/phing
```

We are always looking forward for your bugreports, feature requests and pull requests. Thank you.

## Code of Conduct

This project adheres to a [Contributor Code of Conduct](https://github.com/slevomat/coding-standard/blob/master/CODE_OF_CONDUCT.md). By participating in this project and its community, you are expected to uphold this code.
