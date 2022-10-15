## Arrays

#### SlevomatCodingStandard.Arrays.DisallowImplicitArrayCreation

Disallows implicit array creation.

#### SlevomatCodingStandard.Arrays.MultiLineArrayEndBracketPlacement 🔧

Enforces reasonable end bracket placement for multi-line arrays.

#### SlevomatCodingStandard.Arrays.SingleLineArrayWhitespace 🔧

Checks whitespace in single line array declarations (whitespace between brackets, around commas, ...).

Sniff provides the following settings:

* `spacesAroundBrackets`: number of spaces you require to have around array brackets
* `enableEmptyArrayCheck` (defaults to `false`): enables check for empty arrays

#### SlevomatCodingStandard.Arrays.TrailingArrayComma 🔧

Commas after last element in an array make adding a new element easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line arrays and requires short array syntax `[]`.

Sniff provides the following settings:

* `enableAfterHeredoc`: enables/disables trailing commas after HEREDOC/NOWDOC, default based on PHP version.


