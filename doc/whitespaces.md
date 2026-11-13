## Whitespaces

#### SlevomatCodingStandard.Whitespaces.DuplicateSpaces 🔧

Checks duplicate spaces anywhere because there aren't sniffs for every part of code to check formatting.

Sniff provides the following settings:

* `ignoreSpacesBeforeAssignment`: to allow multiple spaces to align assignments.
* `ignoreSpacesInAnnotation`: to allow multiple spaces to align annotations.
* `ignoreSpacesInComment`: to allow multiple spaces to align content of the comment.
* `ignoreSpacesInParameters`: to allow multiple spaces to align parameters.
* `ignoreSpacesInMatch`: to allow multiple spaces to align `match` expressions.

#### SlevomatCodingStandard.Whitespaces.MultiLineOperatorIndentation 🔧

Enforces leading placement and exact indentation of multiline string concatenation (`.`) and configurable operators.

Sniff provides the following settings:

* `operators`: optional list of operator token names or codes to check (default: `[T_STRING_CONCAT]`).
