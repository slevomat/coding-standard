## Arrays

#### SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys 🔧

Arrays should be defined with keys in alphabetical order.
It defines where new entries should be inserted.
It reduces merge conflicts and duplicate entries.

This sniff enforces natural sorting of array definitions by key in multi-line arrays.

#### SlevomatCodingStandard.Arrays.ArrayAccess 🔧

Disallow whitespace between array access operator and the variable, or between array access operators.

#### SlevomatCodingStandard.Arrays.DisallowImplicitArrayCreation

Disallows implicit array creation.

#### SlevomatCodingStandard.Arrays.DisallowPartiallyKeyed 🚧

Array must have keys specified for either all or none of the values.

#### SlevomatCodingStandard.Arrays.MultiLineArrayEndBracketPlacement 🔧

Enforces reasonable end bracket placement for multi-line arrays.

#### SlevomatCodingStandard.Arrays.SingleLineArrayWhitespace 🔧

Checks whitespace in single line array declarations (whitespace between brackets, around commas, ...).

Sniff provides the following settings:

* `spacesAroundBrackets`: number of spaces you require to have around array brackets
* `enableEmptyArrayCheck` (default: `false`): enables check for empty arrays

#### SlevomatCodingStandard.Arrays.TrailingArrayComma 🔧

Commas after last element in an array make adding a new element easier and result in a cleaner versioning diff.

This sniff enforces trailing commas in multi-line arrays.

Sniff provides the following settings:

* `enableAfterHeredoc`: enables/disables trailing commas after HEREDOC/NOWDOC, default based on PHP version.
