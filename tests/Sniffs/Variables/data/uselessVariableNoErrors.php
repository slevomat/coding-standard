<?php

return $z;

$a = null;

function returnWithoutVariable() {
	return true;
}

function variableOutsideScope() {
	return $a;
}

function moreComplexReturn() {
	$b = 1;
	return $b + 1;
}

function notAssignment() {
	$c + 1;
	return $c;
}

function sameVariableAfterReturn() {
	$d = 0;

	if (true) {
		return $d;
	}

	$d = 1;
}

function differentVariable() {
	$e = 10;
	return $f;
}

function staticVariable() {
	static $g = null;
	return $g;
}

function uglyStaticVariable() {
	static $g;

	if (is_string($g)) {
		return $g;
	}

	$g = 'g';

	return $g;
}

function withDocComment() {
	/** @var string $h */
	$h = 'h';
	return $h;
}

function withGenericDocComment() {
	/** @var array<int, string> $hh */
	$hh = [];
	return $hh;
}

function withPhpsanDocComment() {
	/** @phpstan-var string $hhh */
	$hhh = [];
	return $hhh;
}

function withPsalmDocComment() {
	/** @psalm-var string $hhhh */
	$hhhh = [];
	return $hhhh;
}

function moreAssignments() {
	$i = 'i';
	$i .= 'ii';
	return $i;
}

function moreAssignmentsWithIf() {
	$i = 'i';
	if (true) {
		$i .= 'ii';
	}
	$i .= 'ii';
	return $i;
}

function moreAssignmentsWithFor($values) {
	$i = 'i';
	for ($x = 0; $x < count($values); $x++) {
		$i .= $values[$x];
	}
	$i .= 'ii';
	return $i;
}

function moreAssignmentsWithForeach($values) {
	$i = 'i';
	foreach ($values as $value) {
		$i .= $value;
	}
	$i .= 'ii';
	return $i;
}

function moreAssignmentsWithWhile($values) {
	$i = 'i';
	while ($value = current($values)) {
		$i .= $value;
	}
	$i .= 'ii';
	return $i;
}

function moreAssignmentsWithDo($values) {
	$i = 'i';
	$value = current($values);
	do {
		$i .= $value;
	} while ($value = next($values));
	$i .= 'ii';
	return $i;
}

function somethingBetweenAssignmentAndReturn() {
	$j = 'j';
	doSomething();
	return $j;
}

function differentScope() {
	$k = 'k';

	if (true) {
		return $k;
	}
}

function assignmentInCondition() {
	if ($l = 'l') {
		$this->doSomething();
		return $l;
	}

}

function assignmentInConditionAgain($file) {
	if (file_exists($path = realpath($file))) {
		doAnything();
		return $path;
	}

	return null;
}

class Foo
{

	protected static $bar = [];

	public static function popBar() : array
	{
		$bar = self::$bar;
		self::$bar = [];

		return $bar;
	}

}

function &returnsReference($result)
{
	$value = $result->getValue();
	return $value;
}

function foo( $bar ) {
	$bar .= ' more bar';
	return $bar;
}

function boo($backtrace) {
    for ($class = $backtrace[$frame]['class']; ($parent = get_parent_class($class)) !== false; $class = $parent);
    return $class;
}

function assignmentAsFunctionParameter() {
	doSomething($p = 0);
	return $p;
}

function assignmentAfterAssignment() {
	doSomething($qq = $q = 0);
	return $q;
}
