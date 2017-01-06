<?php

$foo === 123;
$foo === true;
$foo === false;
$foo === null;
$foo === [];
BAR === 123;
Foo::BAR === 123;
Foo::BAR === 123.0;
$e === \Foo\Bar::BAR;
foo() === Foo::BAR;
foo() + 2 === Foo::BAR;
$foo === Foo::BAR;
$foo + 2 === Foo::BAR;
$this->foo() === Foo::BAR;
$foo === -1;
$foo === +1;
(foo() === BAR|| (
	Foo::BAR === ['test'])) ? Foo::BAR === 123.0: $foo === null;

if (
	$foo($bar) === [Foo::BAR, Foo::BAZ] && (
		$bar === true ||
		$bar === null
	)
) {
}
