<?php

return $z;

$a = null;

function returnWithoutVariable() {
	return true;
}

function varibleOutsideScope() {
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

function moreAssigments() {
	$i = 'i';
	$i .= 'ii';
	return $i;
}

function moreAssigmentsWithIf() {
	$i = 'i';
	if (true) {
		$i .= 'ii';
	}
	$i .= 'ii';
	return $i;
}

function moreAssigmentsWithFor($values) {
	$i = 'i';
	for ($x = 0; $x < count($values); $x++) {
		$i .= $values[$x];
	}
	$i .= 'ii';
	return $i;
}

function moreAssigmentsWithForeach($values) {
	$i = 'i';
	foreach ($values as $value) {
		$i .= $value;
	}
	$i .= 'ii';
	return $i;
}

function moreAssigmentsWithWhile($values) {
	$i = 'i';
	while ($value = current($values)) {
		$i .= $value;
	}
	$i .= 'ii';
	return $i;
}

function moreAssigmentsWithDo($values) {
	$i = 'i';
	$value = current($values);
	do {
		$i .= $value;
	} while ($value = next($values));
	$i .= 'ii';
	return $i;
}

function somethingBetweenAssigmentAndReturn() {
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

function assigmentInCondition() {
	if ($l = 'l') {
		$this->doSomething();
		return $l;
	}

}

function assigmentInConditionAgain($file) {
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
