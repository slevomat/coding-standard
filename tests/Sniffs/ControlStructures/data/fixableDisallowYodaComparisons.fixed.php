<?php

$foo === 123;
$foo === true;
$foo === false;
$foo === null;
$foo === [];
$foo === array();
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
(foo() === BAR || (
	Foo::BAR === ['test']
)) ? Foo::BAR === 123.0
	: $foo === null;
(foo() === BAR || (
	Foo::BAR === array('test')
)) ? Foo::BAR === 123.0
	: $foo === null;

if (
	$foo($bar) === [Foo::BAR, Foo::BAZ] && (
		$bar === true ||
		$bar === null
	)
) {
}
if (
	$foo($bar) === array(Foo::BAR, Foo::BAZ) && (
		$bar === true ||
		$bar === null
	)
) {
}

$x = $a ?? $b === 123;

switch ($c) {
	case $d === 123:
		break;
}

$$a === $x;
(int) $bar === FOO;

$x = [$username === self::ADMIN_EMAIL ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
$x = [$username === self::ADMIN_EMAIL ? self::ROLE_ADMIN : self::ROLE_CUSTOMER];
$x = array($username === self::ADMIN_EMAIL ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);
$x = array($username === self::ADMIN_EMAIL ? self::ROLE_ADMIN : self::ROLE_CUSTOMER);

$x = array($username === array() ? true : false);
$x = array($username === [] ? true : false);
$x = [$username === array() ? true : false];
$x = [$username === [] ? true : false];
$x = $username === [$a, $b, $c];

$param === A::TYPE_A and $param === A::TYPE_B;
$param === A::TYPE_A or $param === A::TYPE_B;
$param === A::TYPE_A xor $param === A::TYPE_B;

if (null === $env = $parameters['env']) {
	// ...
}
