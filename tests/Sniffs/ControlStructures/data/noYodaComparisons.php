<?php

$foo === $bar;
$foo === 123;
$foo === true;
$foo === false;
$foo === null;
$foo === [];
$foo === $this->foo();
$this->foo() === $foo;
[123] === [123];
BAR === 123;
Foo::BAR === 123;
Foo::BAR === 123.0;
$e === \Foo\Bar\Baz::BAR;
$foo === Foo::BAR;
$foo + 2 === Foo::BAR;
$this->foo() === Foo::BAR;
count($cartItem->getReservations()) !== $neededReservationsAmount;
$optionalPartOpeningBracePosition !== strlen($part) - 1;
$optionalPartOpeningBracePosition !== \Nette\Utils\Strings::length($part) - 1;
foo() + 2 === Foo::BAR;

if (
	$foo($bar) === [Foo::BAR, Foo::BAZ] && (
		$bar === true ||
		$bar === null
	)
) {
}
