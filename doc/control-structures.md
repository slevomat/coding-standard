## Control structures

#### SlevomatCodingStandard.ControlStructures.AssignmentInCondition

Disallows assignments in `if`, `elseif` and `do-while` loop conditions:

```php
if ($file = findFile($path)) {

}
```

Assignment in `while` loop condition is specifically allowed because it's commonly used.

This is a great addition to already existing `SlevomatCodingStandard.ControlStructures.DisallowYodaComparison` because it prevents the danger of assigning something by mistake instead of using a comparison operator like `===`.

Sniff provides the following settings:
* `ignoreAssignmentsInsideFunctionCalls`: ignores assignment inside function calls, like this:

```php
if (in_array(1, $haystack, $strict = true)) {

}
```

#### SlevomatCodingStandard.ControlStructures.BlockControlStructureSpacing 🔧

Enforces configurable number of lines around block control structures (if, foreach, ...).

Sniff provides the following settings:

* `linesCountBefore`: allows to configure the number of lines before control structure.
* `linesCountBeforeFirst`: allows to configure the number of lines before first control structure.
* `linesCountAfter`: allows to configure the number of lines after control structure.
* `linesCountAfterLast`: allows to configure the number of lines after last control structure.
* `controlStructures`: allows to narrow the list of checked control structures.

For example, with the following setting, only `if` and `switch` keywords are checked.

```xml
<rule ref="SlevomatCodingStandard.ControlStructures.BlockControlStructureSpacing">
	<properties>
		<property name="controlStructures" type="array">
			<element value="if"/>
			<element value="switch"/>
		</property>
	</properties>
</rule>
```

#### SlevomatCodingStandard.ControlStructures.EarlyExit 🔧

Requires use of early exit.

Sniff provides the following settings:

* `ignoreStandaloneIfInScope`: ignores `if` that is standalone in scope, like this:

```php
foreach ($values as $value) {
	if ($value) {
		doSomething();
	}
}
```

* `ignoreOneLineTrailingIf`: ignores `if` that has one line content and is on the last position in scope, like this:

```php
foreach ($values as $value) {
	$value .= 'whatever';

	if ($value) {
		doSomething();
	}
}
```

* `ignoreTrailingIfWithOneInstruction`: ignores `if` that has only one instruction and is on the last position in scope, like this:

```php
foreach ($values as $value) {
	$value .= 'whatever';

	if ($value) {
		doSomething(function () {
			// Anything
		});
	}
}
```

#### SlevomatCodingStandard.ControlStructures.DisallowContinueWithoutIntegerOperandInSwitch 🔧

Disallows use of `continue` without integer operand in `switch` because it emits a warning in PHP 7.3 and higher.

#### SlevomatCodingStandard.ControlStructures.DisallowEmpty

Disallows use of `empty()`.

#### SlevomatCodingStandard.ControlStructures.DisallowNullSafeObjectOperator

Disallows using `?->` operator.

#### SlevomatCodingStandard.ControlStructures.DisallowShortTernaryOperator 🔧

Disallows short ternary operator `?:`.

Sniff provides the following settings:

* `fixable`: the sniff is fixable by default, however in strict code it makes sense to forbid this weakly typed form of ternary altogether, you can disable fixability with this option.

#### SlevomatCodingStandard.ControlStructures.DisallowTrailingMultiLineTernaryOperator 🔧

Ternary operator has to be reformatted when the operator is not leading the line.

```php
# wrong
$t = $someCondition ?
	$thenThis :
	$otherwiseThis;

# correct
$t = $someCondition
	? $thenThis
	: $otherwiseThis;
```

#### SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing 🔧

Enforces configurable number of lines around jump statements (continue, return, ...).

Sniff provides the following settings:

* `allowSingleLineYieldStacking`: whether or not to allow multiple yield/yield from statements in a row without blank lines.
* `linesCountBefore`: allows to configure the number of lines before jump statement.
* `linesCountBeforeFirst`: allows to configure the number of lines before first jump statement.
* `linesCountBeforeWhenFirstInCaseOrDefault`: allows to configure the number of lines before jump statement that is first in `case` or `default`
* `linesCountAfter`: allows to configure the number of lines after jump statement.
* `linesCountAfterLast`: allows to configure the number of lines after last jump statement.
* `linesCountAfterWhenLastInCaseOrDefault`: allows to configure the number of lines after jump statement that is last in `case` or `default`
* `linesCountAfterWhenLastInLastCaseOrDefault`: allows to configure the number of lines after jump statement that is last in last `case` or `default`
* `jumpStatements`: allows to narrow the list of checked jump statements.

For example, with the following setting, only `continue` and `break` keywords are checked.

```xml
<rule ref="SlevomatCodingStandard.ControlStructures.JumpStatementsSpacing">
	<properties>
		<property name="jumpStatements" type="array">
			<element value="continue"/>
			<element value="break"/>
		</property>
	</properties>
</rule>
```

#### SlevomatCodingStandard.ControlStructures.LanguageConstructWithParentheses 🔧

`LanguageConstructWithParenthesesSniff` checks and fixes language construct used with parentheses.

#### SlevomatCodingStandard.ControlStructures.NewWithParentheses 🔧

Requires `new` with parentheses.

#### SlevomatCodingStandard.ControlStructures.NewWithoutParentheses 🔧

Reports `new` with useless parentheses.

#### SlevomatCodingStandard.ControlStructures.RequireMultiLineCondition 🔧

Enforces conditions of `if`, `elseif`, `while` and `do-while` with one or more boolean operators to be split to more lines
so each condition part is on its own line.

Sniff provides the following settings:

* `minLineLength`: specifies minimum line length to enforce condition to be split. Use 0 value to enforce for all conditions, regardless of length.
* `booleanOperatorOnPreviousLine`: boolean operator is placed at the end of previous line when fixing.
* `alwaysSplitAllConditionParts`: require all condition parts to be on its own line - it reports error even if condition is already multi-line but there are some condition parts on the same line.

#### SlevomatCodingStandard.ControlStructures.RequireMultiLineTernaryOperator 🔧

Ternary operator has to be reformatted to more lines when the line length exceeds the given limit.

Sniff provides the following settings:

* `lineLengthLimit` (defaults to `0`)
* `minExpressionsLength` (defaults to `null`): when the expressions after `?` are shorter than this length, the ternary operator does not have to be reformatted.

#### SlevomatCodingStandard.ControlStructures.RequireNullCoalesceEqualOperator 🔧

Requires use of null coalesce equal operator when possible.

This sniff provides the following setting:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 7.4 or higher.

#### SlevomatCodingStandard.ControlStructures.RequireNullCoalesceOperator 🔧

Requires use of null coalesce operator when possible.

#### SlevomatCodingStandard.ControlStructures.RequireNullSafeObjectOperator 🔧

Requires using `?->` operator.

Sniff provides the following settings:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 8.0 or higher.

#### SlevomatCodingStandard.ControlStructures.RequireSingleLineCondition 🔧

Enforces conditions of `if`, `elseif`, `while` and `do-while` to be on a single line.

Sniff provides the following settings:

* `maxLineLength`: specifies max allowed line length. If condition (and the rest of the line) would fit on it, it's enforced. Use 0 value to enforce for all conditions, regardless of length.
* `alwaysForSimpleConditions`: allows to enforce single line for all simple conditions (i.e no `&&`, `||` or `xor`), regardless of length.

#### SlevomatCodingStandard.ControlStructures.RequireShortTernaryOperator 🔧

Requires short ternary operator `?:` when possible.

#### SlevomatCodingStandard.ControlStructures.RequireTernaryOperator 🔧

Requires ternary operator when possible.

Sniff provides the following settings:

* `ignoreMultiLine` (defaults to `false`): ignores multi-line statements.

#### SlevomatCodingStandard.ControlStructures.DisallowYodaComparison 🔧
#### SlevomatCodingStandard.ControlStructures.RequireYodaComparison 🔧

[Yoda conditions](https://en.wikipedia.org/wiki/Yoda_conditions) decrease code comprehensibility and readability by switching operands around comparison operators forcing the reader to read the code in an unnatural way.

Sniff provides the following settings:

* `alwaysVariableOnRight` (defaults to `false`): moves variables always to right.

`DisallowYodaComparison` looks for and fixes such comparisons not only in `if` statements but in the whole code.

However, if you prefer Yoda conditions, you can use `RequireYodaComparison`.

#### SlevomatCodingStandard.ControlStructures.UselessIfConditionWithReturn 🔧

Reports useless conditions where both branches return `true` or `false`.

Sniff provides the following settings:

* `assumeAllConditionExpressionsAreAlreadyBoolean` (defaults to `false`).

#### SlevomatCodingStandard.ControlStructures.UselessTernaryOperator 🔧

Reports useless ternary operator where both branches return `true` or `false`.

Sniff provides the following settings:

* `assumeAllConditionExpressionsAreAlreadyBoolean` (defaults to `false`).
