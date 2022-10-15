## Numbers

#### SlevomatCodingStandard.Numbers.DisallowNumericLiteralSeparator 🔧

Disallows numeric literal separators.

#### SlevomatCodingStandard.Numbers.RequireNumericLiteralSeparator

Requires use of numeric literal separators.

This sniff provides the following setting:

* `enable`: either to enable or not this sniff. By default, it is enabled for PHP versions 7.4 or higher.
* `minDigitsBeforeDecimalPoint`: the minimum digits before decimal point to require separator.
* `minDigitsAfterDecimalPoint`: the minimum digits after decimal point to require separator.
* `ignoreOctalNumbers`: to ignore octal numbers.
