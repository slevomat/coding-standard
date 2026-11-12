<?php // lint >= 8.0

namespace SlevomatCodingStandard\Helpers;

// Line 6: is() test - nullable type hint (false)
function testNullableType(?int $a): void
{
}

// Line 11: is() test - ternary operator (true)
$a = $b ? 1 : 2;

// Line 14: isConditionOfTernaryOperator() - $x is false, = is false, $b is true, $b on line 186 is false
$x = $b ? 1 : 2;

// Line 17: getElsePointer() - simple ternary
$res1 = $a ? 1 : 2;

// Line 20: getElsePointer() - nested parentheses in then-branch
$res2 = $a ? (1 + 2) : 3;

// Line 23: getElsePointer() - nested short array in then-branch
$res3 = $a ? [1, 2] : 3;

// Line 26: getElsePointer() - nested square bracket indexing in then-branch
$res4 = $a ? $b[0] : 3;

// Line 29: getElsePointer() - nested unparenthesized ternary in then-branch
$res5 = $a ? $b ? 1 : 2 : 3;

// Line 32: getElsePointer() - closure in then-branch (inline then and else in different scope)
$res6 = $a ? static function () {
	return true ? 1 : 2;
} : 3;

// Line 37: getStartPointer() - parenthesized condition
$s1 = ($a === 1) ? 1 : 2;

// Line 40: getStartPointer() - condition ending with array access
$s2 = $b[0] ? 1 : 2;

// Line 43: getStartPointer() - condition ending with short array
$s3 = [1, 2] ? 1 : 2;

// Line 46: getStartPointer() - return delimiter
return $a ? 1 : 2;

// Line 49: getStartPointer() - throw delimiter
throw $a ? new \Exception() : new \LogicException();

// Line 52: getStartPointer() - echo delimiter
echo $a ? 1 : 2;

// Line 55: getStartPointer() - print delimiter
print $a ? 1 : 2;

// Line 59: getStartPointer() - case delimiter
switch ($foo) {
	case $a ? 1 : 2:
		break;
}

// Line 64: getStartPointer() - array key/value double arrow delimiter
$arr1 = ['key' => $a ? 1 : 2];

// Line 67: getStartPointer() - array element open bracket delimiter
$arr2 = [$a ? 1 : 2];

// Line 70: getStartPointer() - bracket indexing open square bracket delimiter
$val = $foo[$a ? 1 : 2];

// Line 73: getStartPointer() - parenthesis delimiter
$paren = ($a ? 1 : 2);

// Line 76: getStartPointer() - comma delimiter
foo($x, $a ? 1 : 2);

// Line 79: getStartPointer() - assignment operator delimiter
$x += $a ? 1 : 2;

// Line 82: getEndPointer() - semicolon delimiter
$e1 = $a ? 1 : 2;

// Line 85: getEndPointer() - balanced parenthesis in else-branch
$e2 = $a ? 1 : foo(2, 3);

// Line 88: getEndPointer() - balanced short array in else-branch
$e3 = $a ? 1 : [2, 3];

// Line 91: getEndPointer() - balanced square bracket indexing in else-branch
$e4 = $a ? 1 : $arr[2];

// Line 94: getEndPointer() - balanced match curly braces in else-branch
$e5 = $a ? 1 : match ($x) {
	default => 2,
};

// Line 99: getEndPointer() - outer close parenthesis delimiter
($a ? 1 : 2);

// Line 102: getEndPointer() - outer close short array delimiter
[$a ? 1 : 2];

// Line 105: getEndPointer() - outer close square bracket delimiter
$bar[$a ? 1 : 2];

// Line 108: getEndPointer() - comma in array (no inner paren/bracket)
[$a ? 1 : 2, 3];

// Line 111: getEndPointer() - comma in array (inner paren closed before comma)
[$a ? 1 : foo(1, 2), 3];

// Line 114: getEndPointer() - comma in array (inner short array closed before comma)
[$a ? 1 : [1, 2], 3];

// Line 117: getEndPointer() - double arrow in array (no inner short array)
[$a ? 1 : 2 => 3];

// Line 120: getEndPointer() - double arrow in array (inner short array in then-branch)
[$a ? [1, 2] : 2 => 3];

// Line 124: getEndPointer() - colon delimiter in switch case
switch ($bar) {
	case $a ? 1 : 2:
		break;
}

// Line 129: getEndPointer() - subsequent unparenthesized ternary operator
$a ?: $b ?: $c;

// Line 132: getEndPointer() - logical and operator
$log1 = $a ? 1 : 2 and $c;

// Line 135: getEndPointer() - logical or operator
$log2 = $a ? 1 : 2 or $c;

// Line 138: getEndPointer() - logical xor operator
$log3 = $a ? 1 : 2 xor $c;

// Line 141: getEndPointer() - closure in else-branch (semicolon inside closure in different scope)
$e6 = $a ? 1 : static function () {
	return 2;
};

// Line 146: ignoreConflictingIndentationSniffs() - short ternary
$i1 = $a
	?: $b;

// Line 150: ignoreConflictingIndentationSniffs() - trailing ? multiline
$i2 = $a ?
	1 :
	2;

// Line 155: ignoreConflictingIndentationSniffs() - leading ? and trailing : multiline
$i3 = $a
	? 1 :
	2;

// Line 160: ignoreConflictingIndentationSniffs() - leading ? and leading : multiline
$i4 = $a
	? 1
	: 2;

// Line 165: ignoreConflictingIndentationSniffs() - inline ? and leading : multiline
$i5 = $a ? 1
	: 2;

// Line 169: hasUnparenthesizedTernary() - parenthesized ternary (false)
$h1 = ($a ? 1 : 2);

// Line 172: hasUnparenthesizedTernary() - array containing ternary (false)
$h2 = [$a ? 1 : 2];

// Line 175: hasUnparenthesizedTernary() - square bracket containing ternary (false)
$h3 = $arr[$a ? 1 : 2];

// Line 178: hasUnparenthesizedTernary() - match containing ternary (false)
$h4 = match ($x) {
	default => $a ? 1 : 2,
};

// Line 183: hasUnparenthesizedTernary() - top-level unparenthesized ternary (true)
$h5 = $a ? 1 : 2;

// Line 186: hasUnparenthesizedTernary() - expression without ternary (false)
$h6 = $a + $b;

$n1 = $a ? 1 : new class {};

$n2 = $a ? 1 : fn (): int => 2;

foo(bar: $a ? 1 : 2);

$m = match ($x) { 1 => $a ? 1 : 2 };

$f = fn () => $a ? 1 : 2;

$c1 = $flag && function (): void {} !== null ? 1 : 2;

$c2 = $flag && new class () { public function foo(): void {} } !== null ? 1 : 2;

$c3 = $flag && match ($x) { default => 1 } !== null ? 1 : 2;

$c4 = $obj->{$prop} ? 1 : 2;

$e23 = [$a === 1 ? true : false ?? [1] => 2];

$y1 = function () use ($a) {
	yield $a ? 1 : 2;
	yield from $a ? 1 : 2;
};

$em1 = match ($x) { 0 => 42, $a ? 1 : 2 => 3, default => 0 };

$em2 = match ($x) { 1 => $a ? 1 : 2 };
