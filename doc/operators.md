## Operators

#### SlevomatCodingStandard.Operators.DisallowEqualOperators 🔧

Disallows using loose `==` and `!=` comparison operators. Use `===` and `!==` instead, they are much more secure and predictable.

#### SlevomatCodingStandard.Operators.DisallowIncrementAndDecrementOperators

Disallows using `++` and `--` operators.

#### SlevomatCodingStandard.Operators.MultiLineOperatorIndentation 🔧

Enforces leading placement and exact indentation of multiline string concatenation (`.`) and configurable operators.

Sniff provides the following settings:

* `operators`: optional list of operator token names or codes to check (default: `[T_STRING_CONCAT]`).

#### SlevomatCodingStandard.Operators.NegationOperatorSpacing 🔧

Checks if there is the same number of spaces after negation operator as expected.

Sniff provides the following settings:

* `spacesCount`: the number of spaces expected after the negation operator

#### SlevomatCodingStandard.Operators.RequireCombinedAssignmentOperator 🔧

Requires using combined assignment operators, eg `+=`, `.=` etc.

#### SlevomatCodingStandard.Operators.RequireOnlyStandaloneIncrementAndDecrementOperators

Reports `++` and `--` operators not used standalone.

#### SlevomatCodingStandard.Operators.SpreadOperatorSpacing 🔧

Enforces configurable number of spaces after the `...` operator.

Sniff provides the following settings:

* `spacesCountAfterOperator`: the number of spaces after the `...` operator.
