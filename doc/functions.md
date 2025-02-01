## Functions

#### SlevomatCodingStandard.Functions.ArrowFunctionDeclaration 🔧

Checks `fn` declaration.

Sniff provides the following settings:

* `spacesCountAfterKeyword`: the number of spaces after `fn`.
* `spacesCountBeforeArrow`: the number of spaces before `=>`.
* `spacesCountAfterArrow`: the number of spaces after `=>`.
* `allowMultiLine`: allows multi-line declaration.

#### SlevomatCodingStandard.Functions.DisallowArrowFunction

Disallows arrow functions.

#### SlevomatCodingStandard.Functions.DisallowEmptyFunction

Reports empty functions body and requires at least a comment inside.

#### SlevomatCodingStandard.Functions.FunctionLength

Disallows long functions. This sniff provides the following setting:

* `includeComments` (default: `false`): should comments be included in the count.
* `includeWhitespace` (default: `false`): should empty lines be included in the count.
* `maxLinesLength` (default: `20`): specifies max allowed function lines length.

#### SlevomatCodingStandard.Functions.RequireArrowFunction 🔧

Requires arrow functions.

Sniff provides the following settings:

* `allowNested` (default: `true`)
* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 7.4 or higher.

#### SlevomatCodingStandard.Functions.RequireMultiLineCall 🔧

Enforces function call to be split to more lines so each parameter is on its own line.

Sniff provides the following settings:

* `minLineLength`: specifies min line length to enforce call to be split. Use 0 value to enforce for all calls, regardless of length.

#### SlevomatCodingStandard.Functions.RequireSingleLineCall 🔧

Enforces function call to be on a single line.

Sniff provides the following settings:

* `maxLineLength`: specifies max allowed line length. If call would fit on it, it's enforced. Use 0 value to enforce for all calls, regardless of length.
* `ignoreWithComplexParameter` (default: `true`): ignores calls with arrays, closures, arrow functions and nested calls.

#### SlevomatCodingStandard.Functions.DisallowNamedArguments

This sniff disallows usage of named arguments.

#### SlevomatCodingStandard.Functions.NamedArgumentSpacing 🔧

Checks spacing in named argument.

#### SlevomatCodingStandard.Functions.DisallowTrailingCommaInCall 🔧

This sniff disallows trailing commas in multi-line calls.

This sniff provides the following setting:

* `onlySingleLine`: to enable checks only for single-line calls.

#### SlevomatCodingStandard.Functions.RequireTrailingCommaInCall 🔧

Commas after the last parameter in function or method call make adding a new parameter easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line calls.

This sniff provides the following setting:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 7.3 or higher.

#### SlevomatCodingStandard.Functions.DisallowTrailingCommaInClosureUse 🔧

This sniff disallows trailing commas in multi-line `use` of closure declaration.

This sniff provides the following setting:

* `onlySingleLine`: to enable checks only for single-line `use` declarations.

#### SlevomatCodingStandard.Functions.RequireTrailingCommaInClosureUse 🔧

Commas after the last inherited variable in multi-line `use` of closure declaration make adding a new variable easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line declarations.

This sniff provides the following setting:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 8.0 or higher.

#### SlevomatCodingStandard.Functions.DisallowTrailingCommaInDeclaration 🔧

This sniff disallows trailing commas in multi-line declarations.

This sniff provides the following setting:

* `onlySingleLine`: to enable checks only for single-line declarations.

#### SlevomatCodingStandard.Functions.RequireTrailingCommaInDeclaration 🔧

Commas after the last parameter in function or method declaration make adding a new parameter easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line declarations.

This sniff provides the following setting:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 8.0 or higher.

#### SlevomatCodingStandard.Functions.StaticClosure 🔧

Reports closures not using `$this` that are not declared `static`.

#### SlevomatCodingStandard.Functions.StrictCall

Some functions have `$strict` parameter. This sniff reports calls to these functions without the parameter or with `$strict = false`.

#### SlevomatCodingStandard.Functions.UnusedInheritedVariablePassedToClosure 🔧

Looks for unused inherited variables passed to closure via `use`.

#### SlevomatCodingStandard.Functions.UnusedParameter 🚧

Looks for unused parameters.

#### SlevomatCodingStandard.Functions.UselessParameterDefaultValue 🚧

Looks for useless parameter default value.
