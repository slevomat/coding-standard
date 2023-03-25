## PHP

#### SlevomatCodingStandard.PHP.DisallowDirectMagicInvokeCall 🔧

Disallows direct call of `__invoke()`.

#### SlevomatCodingStandard.PHP.DisallowReference

Sniff disallows usage of references.

#### SlevomatCodingStandard.PHP.ForbiddenClasses 🔧

Reports usage of forbidden classes, interfaces, parent classes and traits. And provide the following settings:

* `forbiddenClasses`: forbids creating instances with `new` keyword or accessing with `::` operator
* `forbiddenExtends`: forbids extending with `extends` keyword
* `forbiddenInterfaces`: forbids usage in `implements` section
* `forbiddenTraits`: forbids imports with `use` keyword

Optionally can be passed as an alternative for auto fixes. See `phpcs.xml` file example:

```xml
<rule ref="SlevomatCodingStandard.PHP.ForbiddenClasses">
	<properties>
		<property name="forbiddenClasses" type="array">
			<element key="Validator" value="Illuminate\Support\Facades\Validator"/>
		</property>
		<property name="forbiddenTraits" type="array">
			<element key="\AuthTrait" value="null"/>
		</property>
	</properties>
</rule>
```

#### SlevomatCodingStandard.PHP.ReferenceSpacing 🔧

Enforces configurable number of spaces after reference.

Sniff provides the following settings:

* `spacesCountAfterReference`: the number of spaces after `&`.

#### SlevomatCodingStandard.PHP.RequireExplicitAssertion 🔧

Requires assertion via `assert` instead of inline documentation comments.

Sniff provides the following settings:

* `enableIntegerRanges` (defaults to `false`): enables support for `positive-int`, `negative-int` and `int<0, 100>`.
* `enableAdvancedStringTypes` (defaults to `false`): enables support for `callable-string`, `numeric-string` and `non-empty-string`.

#### SlevomatCodingStandard.PHP.RequireNowdoc 🔧

Requires nowdoc syntax instead of heredoc when possible.

#### SlevomatCodingStandard.PHP.OptimizedFunctionsWithoutUnpacking

PHP optimizes some internal functions into special opcodes on VM level. Such optimization results in much faster execution compared to calling standard functions. This only works when these functions are not invoked with argument unpacking (`...`).

The list of these functions varies across PHP versions, but is the same as functions that must be referenced by their global name (either by `\ ` prefix or using `use function`), not a fallback name inside namespaced code.

#### SlevomatCodingStandard.PHP.ShortList 🔧

Enforces using short form of list syntax, `[...]` instead of `list(...)`.

#### SlevomatCodingStandard.PHP.TypeCast 🔧

Enforces using shorthand cast operators, forbids use of unset and binary cast operators: `(bool)` instead of `(boolean)`, `(int)` instead of `(integer)`, `(float)` instead of `(double)` or `(real)`. `(binary)` and `(unset)` are forbidden.

#### SlevomatCodingStandard.PHP.UselessParentheses 🔧

Looks for useless parentheses.

Sniff provides the following settings:

* `ignoreComplexTernaryConditions` (defaults to `false`): ignores complex ternary conditions - condition must contain `&&`, `||` etc. or end of line.

#### SlevomatCodingStandard.PHP.UselessSemicolon 🔧

Looks for useless semicolons.
